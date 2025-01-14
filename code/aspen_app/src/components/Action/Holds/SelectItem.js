import { Icon, ChevronDownIcon, FormControl, FormControlLabel, FormControlLabelText, Select, SelectTrigger, SelectInput, SelectIcon, SelectPortal, SelectBackdrop, SelectContent, SelectDragIndicatorWrapper, SelectDragIndicator, SelectItem, CheckIcon, Radio, RadioGroup, RadioIndicator, RadioIcon, RadioLabel, CircleIcon } from '@gluestack-ui/themed';
import React from 'react';
import { Platform } from 'react-native';
import _ from 'lodash';
import { ThemeContext } from '../../../context/initialContext';
import { getTermFromDictionary } from '../../../translations/TranslationService';

export const SelectItemHold = (props) => {
     const { id, data, item, setItem, holdType, setHoldType, showModal, holdTypeForFormat, language, url, textColor, theme } = props;

     let copies = data.copies;
     let copyKeys = Object.keys(copies);
     let key = copyKeys[0];
     let defaultItem = copies[key].id;

     if (item) {
          defaultItem = item;
     }

     /*if (defaultItem && !item) {
          setItem(defaultItem);
     }*/

     return (
          <>
               {holdTypeForFormat === 'either' ? (
                    <FormControl>
                         <RadioGroup
                              name="holdTypeGroup"
                              value={holdType}
                              onChange={(nextValue) => {
                                   setHoldType(nextValue);
                                   setItem('');
                              }}
                              accessibilityLabel="">
                              <Radio value="default" my="$1" size="sm">
                                   <RadioIndicator mr="$1">
                                        <RadioIcon as={CircleIcon} strokeWidth={1} />
                                   </RadioIndicator>
                                   <RadioLabel color={textColor}>{getTermFromDictionary(language, 'first_available')}</RadioLabel>
                              </Radio>
                              <Radio value="item" my="$1" size="sm">
                                   <RadioIndicator mr="$1">
                                        <RadioIcon as={CircleIcon} strokeWidth={1} />
                                   </RadioIndicator>
                                   <RadioLabel color={textColor}>{getTermFromDictionary(language, 'specific_item')}</RadioLabel>
                              </Radio>
                         </RadioGroup>
                    </FormControl>
               ) : null}
               {holdTypeForFormat === 'item' || holdType === 'item' ? (
                    <FormControl>
                         <FormControlLabel>
                              <FormControlLabelText color={textColor}>{getTermFromDictionary(language, 'select_item')}</FormControlLabelText>
                         </FormControlLabel>
                         <Select
                              isReadOnly={Platform.OS === 'android'}
                              name="itemForHold"
                              selectedValue={defaultItem}
                              minWidth={200}
                              defaultValue={defaultItem}
                              accessibilityLabel={getTermFromDictionary(language, 'select_item')}
                              _selectedItem={{
                                   bg: 'tertiary.300',
                                   endIcon: <CheckIcon size="5" />,
                              }}
                              mt="$1"
                              mb="$2"
                              onValueChange={(itemValue) => setItem(itemValue)}>
                              <SelectTrigger variant="outline" size="md">
                                   {_.map(Object.keys(copies), function (item, index, array) {
                                        let copy = copies[item];
                                        console.log(copy);
                                        if (copy.id === defaultItem) {
                                             return <SelectInput value={copy.location} color={textColor} />;
                                        }
                                   })}
                                   <SelectIcon mr="$3">
                                        <Icon as={ChevronDownIcon} color={textColor} />
                                   </SelectIcon>
                              </SelectTrigger>
                              <SelectPortal>
                                   <SelectBackdrop />
                                   <SelectContent p="$5">
                                        <SelectDragIndicatorWrapper>
                                             <SelectDragIndicator />
                                        </SelectDragIndicatorWrapper>
                                        {_.map(Object.keys(copies), function (item, index, array) {
                                             let copy = copies[item];
                                             if (copy.id === defaultItem) {
                                                  return <SelectItem label={copy.location} value={copy.id} key={copy.id} bgColor={theme['colors']['tertiary']['300']} />;
                                             }
                                             return <SelectItem label={copy.location} value={copy.id} key={copy.id} />;
                                        })}
                                   </SelectContent>
                              </SelectPortal>
                         </Select>
                    </FormControl>
               ) : null}
          </>
     );
};