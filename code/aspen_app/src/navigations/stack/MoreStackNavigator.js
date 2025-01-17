import { createNativeStackNavigator } from '@react-navigation/native-stack';
import React from 'react';
import { LanguageContext } from '../../context/initialContext';
import { AllLocations } from '../../screens/Library/AllLocations';
import { Location } from '../../screens/Library/Location';

import { MyLibrary } from '../../screens/Library/MyLibrary';
import { MoreMenu } from '../../screens/More/MoreMenu';
import { Settings_BrowseCategories } from '../../screens/MyAccount/Settings/BrowseCategories';
import { Settings_LanguageScreen } from '../../screens/MyAccount/Settings/Language';
import { Settings_NotificationOptions } from '../../screens/MyAccount/Settings/NotificationOptions';
import { PreferencesScreen } from '../../screens/MyAccount/Settings/Preferences';
import { SupportScreen } from '../../screens/MyAccount/Settings/Support';
import { getTermFromDictionary } from '../../translations/TranslationService';

const MoreStackNavigator = () => {
     const { language } = React.useContext(LanguageContext);
     const Stack = createNativeStackNavigator();
     return (
          <Stack.Navigator
               initialRouteName="MoreMenu"
               screenOptions={{
                    headerShown: true,
                    headerBackTitleVisible: false,
               }}>
               <Stack.Screen name="MoreMenu" component={MoreMenu} options={{ title: getTermFromDictionary(language, 'nav_more') }} />
               <Stack.Screen
                    name="AllLocations"
                    component={AllLocations}
                    options={({ route }) => ({
                         title: getTermFromDictionary(language, 'locations'),
                    })}
               />
               <Stack.Screen
                    name="Location"
                    component={Location}
                    options={({ route }) => ({
                         title: route?.params?.title ?? getTermFromDictionary(language, 'location'),
                    })}
               />
               <Stack.Screen
                    name="MyLibrary"
                    component={MyLibrary}
                    options={({ route }) => ({
                         title: route?.params?.title ?? getTermFromDictionary(language, 'my_library'),
                    })}
               />
               <Stack.Group>
                    <Stack.Screen name="MyPreferences" component={PreferencesScreen} options={{ title: getTermFromDictionary(language, 'preferences') }} />
                    <Stack.Screen name="MyPreferences_ManageBrowseCategories" component={Settings_BrowseCategories} options={{ title: getTermFromDictionary(language, 'manage_browse_categories') }} />
                    <Stack.Screen name="MyPreferences_Language" component={Settings_LanguageScreen} options={{ title: getTermFromDictionary(language, 'manage_browse_categories') }} />
                    <Stack.Screen name="MyPreferences_Appearance" component={Settings_BrowseCategories} options={{ title: getTermFromDictionary(language, 'manage_browse_categories') }} />
                    <Stack.Screen name="MyDevice_Notifications" component={Settings_NotificationOptions} options={{ title: getTermFromDictionary(language, 'notification_settings') }} />
                    <Stack.Screen name="MyDevice_Support" component={SupportScreen} options={{ title: getTermFromDictionary(language, 'support') }} />
               </Stack.Group>
          </Stack.Navigator>
     );
};

export default MoreStackNavigator;