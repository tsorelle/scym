/**
 * Created by Terry on 11/10/2015.
 */
module Tops {
    export class textParser {
        /**
         * format full name from parts
         */
        public static makeFullName(first: string, last:string, middle: string = null) {
            var result = first? first.trim() : '';
            if (middle) {
                result = result + ' ' + middle.trim();
            }
            if (last){
                result = result + ' ' + last.trim();
            }
            return result;
        }
    }

}
