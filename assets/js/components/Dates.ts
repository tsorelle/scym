/**
 * Created by Terry on 2/16/2016.
 */
module Tops {
    export class Dates {
        public static getCurrentDateString() {
            var d = new Date();
            return d.toDateString();
        }

        public static getDayName(d : number, short = false) {
            switch(d) {
                case 0:
                    return short ? 'Sun' : 'Sunday';
                    break;
                case 1:
                    return short ? 'Mon' : 'Monday';
                    break;
                case 2:
                    return short ? 'Tue' : 'Tuesday';
                    break;
                case 3:
                    return short ? 'Wed' : 'Wednesday';
                    break;
                case 4:
                    return short ? 'Thu' : 'Thursday';
                    break;
                case 5:
                    return short ? 'Fri' : 'Friday';
                    break;
                case 6:
                    return short ? 'Sat' : 'Saturday';
                    break;
                default:
                    return 'ERROR: invalid day number';
            }

        }

        public static getMonthName(m : number, short = false) {
            switch(m) {
                case 0 :
                    return short? 'Jan' : 'January';
                case 1 :
                    return short? 'Feb' : 'February';
                case 2 :
                    return short? 'Mar' : 'March';
                case 3 :
                    return short? 'Apr' : 'April';
                case 4 :
                    return 'May';
                case 5 :
                    return 'June';
                case 6 :
                    return 'July';
                case 7 :
                    return short? 'Aug' : 'August';
                case 8 :
                    return short? 'Sept' : 'September';
                case 9  :
                    return short? 'Oct' : 'October';
                case 10 :
                    return short? 'Nov' : 'November';
                case 11 :
                    return short? 'Dec' : 'December';
                default:
                    return 'Error: invalid month number';
            }

        }

        public static formatCurrentDate(formatType : string = 'standard', showDay = false) {
            return Dates.formatDate(new Date(),formatType, showDay);
        }

        public static formatDate(d : Date, formatType : string = 'standard', showDay = false) {
            if (!d) {
                return '';
            }
            var result = '';
            switch (formatType) {
                case 'long' :
                    if (showDay) {
                        result = Dates.getDayName(d.getDay())  + ' ';
                    }
                    result += Dates.getMonthName(d.getMonth()) + ' ' +
                        d.getDate() + ', ' + d.getFullYear();
                    return result;
                case 'intl' :
                    return d.getFullYear() + '-' + (d.getMonth() + 1) + d.getDate();
                    break;
                case 'us' :
                    return d.getDate() + '/' + d.getMonth() + '/' + d.getFullYear();
                    break;
                case 'iso' :
                    return d.toISOString();
                case 'json' :
                    return d.toJSON();
                default :
                    return d.toDateString();
            }
        }

        public static formatDateString(s : string, formatType : string = 'standard', showDay = false) {
            if (!s) {
                return '';
            }
            var d = new Date(s);
            return  Dates.formatDate(d, formatType,showDay);
        }
    }

}
