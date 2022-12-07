import _ from 'lodash';
import moment from 'moment';
import { useNavigation, useFocusEffect } from '@react-navigation/native';
import { Center, Flex, Image, Text, Box, Badge, View } from 'native-base';
import React, { Component } from 'react';
import Barcode from 'react-native-barcode-expo';
import Carousel from 'react-native-reanimated-carousel';
import Animated, { Extrapolate, interpolate, useAnimatedStyle, useSharedValue } from 'react-native-reanimated';
import { Dimensions } from 'react-native';
import * as ScreenOrientation from 'expo-screen-orientation';
import * as Brightness from 'expo-brightness';

// custom components and helper files
import { loadError } from '../../../components/loadError';
import { loadingSpinner } from '../../../components/loadingSpinner';
import { userContext } from '../../../context/user';
import { translate } from '../../../translations/translations';
import { LibrarySystemContext, UserContext } from '../../../context/initialContext';
import { getLinkedAccounts } from '../../../util/api/user';

export const MyLibraryCard = () => {
     const navigation = useNavigation();
     const [isLoading, setLoading] = React.useState(true);
     const [previousBrightness, setPreviousBrightness] = React.useState();
     const [isLandscape, setIsLandscape] = React.useState();
     const { user, accounts, updateLinkedAccounts } = React.useContext(UserContext);
     const { library } = React.useContext(LibrarySystemContext);

     React.useEffect(() => {
          (async () => {
               const { status } = await Brightness.requestPermissionsAsync();
               if (status === 'granted') {
                    const level = await Brightness.getBrightnessAsync();
                    if (level) {
                         console.log('Storing previous screen brightness');
                         setPreviousBrightness(level);
                    }
                    console.log('Updating screen brightness');
                    Brightness.setSystemBrightnessAsync(1);
               } else {
                    console.log('Unable to update screen brightness');
               }
          })();
          (async () => {
               const result = await ScreenOrientation.getOrientationAsync();
               if (result === 3 || result === 4) {
                    setIsLandscape(true);
               } else {
                    setIsLandscape(false);
               }
          })();
          (async () => {
               const result = await ScreenOrientation.getOrientationAsync();
               if (result === 3 || result === 4) {
                    setIsLandscape(true);
               } else {
                    setIsLandscape(false);
               }
          })();
          let response = ScreenOrientation.addOrientationChangeListener(({ orientationInfo, orientationLock }) => {
               switch (orientationInfo.orientation) {
                    case ScreenOrientation.Orientation.LANDSCAPE_LEFT:
                    case ScreenOrientation.Orientation.LANDSCAPE_RIGHT:
                         console.log('Screen orientation changed to landscape');
                         setIsLandscape(true);
                         break;
                    default:
                         console.log('orientation changed to portrait');
                         setIsLandscape(false);
                         break;
               }
          });
          return () => {};
     }, []);

     React.useEffect(() => {
          navigation.addListener('blur', () => {
               (async () => {
                    const { status } = await Brightness.getPermissionsAsync();
                    if (status === 'granted' && previousBrightness) {
                         console.log('Restoring previous screen brightness');
                         Brightness.setSystemBrightnessAsync(previousBrightness);
                    }
               })();
          });
          return () => {};
     }, [navigation, previousBrightness]);

     let cards = [];
     cards.push({
          key: 0,
          displayName: user.displayName,
          cat_username: user.cat_username,
          expired: user.expired,
          expires: user.expires,
          barcodeStyle: library.barcodeStyle,
     });

     useFocusEffect(
          React.useCallback(() => {
               const update = async () => {
                    await getLinkedAccounts(library.baseUrl).then((result) => {
                         if (accounts !== result) {
                              //updateLinkedAccounts(result);
                              //updateCards();
                         }
                         setLoading(false);
                    });
               };
               update().then(() => {
                    return () => update();
               });
          }, [])
     );

     const updateCards = () => {
          if (!_.isEmpty(accounts)) {
               let count = 0;
               accounts.forEach((account) => {
                    count = count + 1;
                    cards.push({
                         key: count,
                         displayName: account.displayName,
                         cat_username: account.cat_username,
                         expired: account.expired,
                         expires: account.expires,
                         barcodeStyle: account.barcodeStyle,
                    });
               });
          }
     };

     const refreshCards = async () => {
          setLoading(true);
          await getLinkedAccounts(library.baseUrl).then((result) => {
               if (accounts !== result) {
                    updateLinkedAccounts(result);
                    updateCards();
               }
               setLoading(false);
          });
     };

     if (isLoading) {
          return loadingSpinner();
     }

     return <CardCarousel cards={cards} orientation={isLandscape} />;
};

const CreateLibraryCard = (data) => {
     const card = data.card;
     const { library } = React.useContext(LibrarySystemContext);

     let barcodeStyle = null;
     if (!_.isUndefined(library.barcodeStyle)) {
          barcodeStyle = _.toString(library.barcodeStyle);
     }

     let barcodeValue = 'UNKNOWN';
     if (!_.isUndefined(card.cat_username)) {
          barcodeValue = card.cat_username;
     }

     let expirationDate;
     if (!_.isUndefined(card.expires)) {
          expirationDate = new Date(card.expires);
     }

     let cardHasExpired = 0;
     if (!_.isUndefined(card.expired)) {
          cardHasExpired = card.expired;
     }

     let neverExpires = false;
     if (cardHasExpired === 0 && _.isDate(expirationDate)) {
          const now = moment().format('MMM D, YYYY');
          const hasExpired = moment(expirationDate).isBefore(now);
          if (hasExpired) {
               neverExpires = true;
          }
     }

     let icon = library.favicon;
     if (library.logoApp) {
          icon = library.logoApp;
     }

     if (barcodeValue === 'UNKNOWN' || _.isNull(barcodeValue) || _.isNull(barcodeStyle)) {
          return (
               <Flex direction="column" bg="white" maxW="90%" px={8} py={5} borderRadius={20}>
                    <Center>
                         <Flex direction="row">
                              {icon ? <Image source={{ uri: icon }} fallbackSource={require('../../../themes/default/aspenLogo.png')} w={42} h={42} alt={translate('user_profile.library_card')} /> : null}
                              <Text bold ml={3} mt={2} fontSize="lg" color="darkText">
                                   {library.displayName}
                              </Text>
                         </Flex>
                    </Center>
                    <Center pt={8}>
                         <Text pb={2} color="darkText">
                              {card.displayName}
                         </Text>
                         <Text color="darkText" bold fontSize="xl">
                              {barcodeValue}
                         </Text>
                         {expirationDate && !neverExpires ? (
                              <Text color="darkText" fontSize={10}>
                                   Expires on {card.expires}
                              </Text>
                         ) : null}
                    </Center>
               </Flex>
          );
     }

     return (
          <Flex direction="column" bg="white" px={8} py={5} borderRadius={20} shadow={1}>
               <Center>
                    <Flex direction="row">
                         {icon ? <Image source={{ uri: icon }} fallbackSource={require('../../../themes/default/aspenLogo.png')} w={42} h={42} alt={translate('user_profile.library_card')} /> : null}
                         <Text bold ml={3} mt={2} fontSize="lg" color="darkText">
                              {library.displayName}
                         </Text>
                    </Flex>
               </Center>
               <Center pt={2}>
                    <Text fontSize="md" color="darkText">
                         {card.displayName}
                    </Text>
               </Center>
               <Center pt={8}>
                    <Barcode value={barcodeValue} format={barcodeStyle} text={barcodeValue} background="warmGray.100" />
                    {expirationDate && !neverExpires ? (
                         <Text color="darkText" fontSize={10} pt={2}>
                              Expires on {card.expires}
                         </Text>
                    ) : null}
               </Center>
          </Flex>
     );
};

const CardCarousel = (data) => {
     const cards = _.sortBy(data.cards, ['key']);
     const isVertical = data.orientation;
     const screenWidth = Dimensions.get('window').width;
     const progressValue = useSharedValue(0);

     let baseOptions = {
          vertical: false,
          width: screenWidth,
          height: screenWidth * 2.5,
     };

     if (isVertical) {
          baseOptions = {
               vertical: true,
               width: screenWidth * 0.5,
               height: screenWidth * 0.6,
          };
     }

     const PaginationItem = (props) => {
          const { animValue, index, length, card, isRotate } = props;
          const width = 80;

          const animStyle = useAnimatedStyle(() => {
               let inputRange = [index - 1, index, index + 1];
               let outputRange = [-width, 0, width];

               if (index === 0 && animValue?.value > length - 1) {
                    inputRange = [length - 1, length, length + 1];
                    outputRange = [-width, 0, width];
               }

               return {
                    transform: [
                         {
                              translateX: interpolate(animValue?.value, inputRange, outputRange, Extrapolate.CLAMP),
                         },
                    ],
               };
          }, [animValue, index, length]);

          return (
               <View
                    style={{
                         width,
                         height: width * 1.5,
                         overflow: 'hidden',
                         transform: [
                              {
                                   rotateZ: isRotate ? '90deg' : '0deg',
                              },
                         ],
                    }}>
                    <Animated.View
                         style={[
                              {
                                   backgroundColor: 'black',
                                   flex: 1,
                              },
                              animStyle,
                         ]}
                    />
                    <Badge>{card.displayName}</Badge>
               </View>
          );
     };

     if (_.size(cards) === 1) {
          const card = cards[0];
          return (
               <Box justifyContent="center" alignItems="center" flex={1} px={3} alignContent="center">
                    <CreateLibraryCard key={0} card={card} />
               </Box>
          );
     }

     return (
          <Box alignItems="center" flex={1} px={3}>
               <Carousel
                    {...baseOptions}
                    pagingEnabled={true}
                    snapEnabled={true}
                    autoPlay={false}
                    mode="parallax"
                    onProgressChange={(_, absoluteProgress) => (progressValue.value = absoluteProgress)}
                    modeConfig={{
                         parallaxScrollingScale: 0.9,
                         parallaxScrollingOffset: 50,
                    }}
                    data={cards}
                    renderItem={({ item, index }) => <CreateLibraryCard key={index} card={item} />}
               />
               {!!progressValue && (
                    <View>
                         {cards.map((card, index) => {
                              return <PaginationItem card={card} animValue={progressValue} index={index} key={index} isRotate={isVertical} length={cards.length} />;
                         })}
                    </View>
               )}
          </Box>
     );
};

/*export default class LibraryCard extends Component {
     static contextType = userContext;

     constructor(props) {
          super(props);
          this.state = {
               isLoading: true,
               hasError: false,
               error: null,
               barcodeStyleInvalid: false,
               library: [],
               location: [],
               linkedAccounts: [],
          };
          this._isMounted = false;
     }

     componentDidMount = async () => {
          this._isMounted = true;
          if (this._isMounted) {
               const libraryContext = JSON.parse(this.props.route.params.libraryContext);
               this.setState({
                    library: libraryContext.library,
               });

               await this.getLinkedAccounts();
          }
     };

     componentWillUnmount() {
          this._isMounted = false;
     }

     getLinkedAccounts = async () => {
          await getLinkedAccounts(this.state.library.baseUrl).then((result) => {
               this.setState({
                    linkedAccounts: result,
                    isLoading: false,
               });
          });
     };

     invalidFormat = () => {
          this.setState({
               barcodeStyleInvalid: true,
          });
     };

     render() {
          const user = this.context.user;
          const library = this.state.library;

          const barcodeStyle = _.toString(library.barcodeStyle);

          let doesNotExpire = false;
          if (!_.isUndefined(user.expired)) {
               if (user.expired === 0) {
                    const now = moment().format('MMM D, YYYY');
                    const expirationDate = new Date(user.expires);
                    const isExpired = moment(expirationDate).isBefore(now);
                    if (isExpired) {
                         doesNotExpire = true;
                    }
               }
          }

          let icon = library.favicon;
          if (library.logoApp) {
               icon = library.logoApp;
          }

          let barcodeValue = 'UNKNOWN';
          if (user.cat_username) {
               barcodeValue = user.cat_username;
          }

          if (this.state.isLoading || user.cat_username === '') {
               return loadingSpinner();
          }

          if (this.state.hasError) {
               return loadError(this.state.error);
          }

          if (_.isNull(barcodeStyle) || this.state.barcodeStyleInvalid) {
               return (
                    <Center flex={1} px={3}>
                         <Flex direction="column" bg="white" maxW="90%" px={8} py={5} borderRadius={20}>
                              <Center>
                                   <Flex direction="row">
                                        <Image source={{ uri: icon }} fallbackSource={require('../../../themes/default/aspenLogo.png')} w={42} h={42} alt={translate('user_profile.library_card')} />
                                        <Text bold ml={3} mt={2} fontSize="lg" color="darkText">
                                             {library.displayName}
                                        </Text>
                                   </Flex>
                              </Center>
                              <Center pt={8}>
                                   <Text pb={2} color="darkText">
                                        {user.displayName}
                                   </Text>
                                   <Text color="darkText" bold fontSize="xl">
                                        {user.cat_username}
                                   </Text>
                                   {user.expires && !doesNotExpire ? (
                                        <Text color="darkText" fontSize={10}>
                                             Expires on {user.expires}
                                        </Text>
                                   ) : null}
                              </Center>
                         </Flex>
                    </Center>
               );
          }

          return (
               <Center flex={1} px={3}>
                    <Flex direction="column" bg="white" maxW="95%" px={8} py={5} borderRadius={20}>
                         <Center>
                              <Flex direction="row">
                                   <Image source={{ uri: icon }} fallbackSource={require('../../../themes/default/aspenLogo.png')} w={42} h={42} alt={translate('user_profile.library_card')} />
                                   <Text bold ml={3} mt={2} fontSize="lg" color="darkText">
                                        {library.displayName}
                                   </Text>
                              </Flex>
                         </Center>
                         <Center pt={8}>
                              <Barcode value={barcodeValue} format={barcodeStyle} text={barcodeValue} background="warmGray.100" onError={() => this.invalidFormat()} />
                              {user.expires && !doesNotExpire ? (
                                   <Text color="darkText" fontSize={10} pt={2}>
                                        Expires on {user.expires}
                                   </Text>
                              ) : null}
                         </Center>
                    </Flex>
               </Center>
          );
     }
}
LibraryCard.contextType = UserContext;*/