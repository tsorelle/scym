/**
 * Created by Terry on 2/19/2015.
 */
///<reference path='../typings/knockout/knockout.d.ts' />
///<reference path='../typings/jquery/jquery.d.ts' />
///<reference path='Peanut.d.ts' />
///<reference path='Debugging.ts' />
module Tops {
    export class KeyValueDTO implements Tops.INameValuePair {
        public Name: string;
        public Value: string;
    }
    export class Peanut {

        public static debugging() {
            if (Tops.Debugging) {
                return Tops.Debugging.isEnabled();
            }
            return false;
        }

        constructor(public clientApp: IPeanutClient) {
            var me = this;
            me.securityToken = me.readSecurityToken();
        }
        // private foo: any;
        // private serviceType: string = 'php';

        static allMessagesType: number = -1;
        static infoMessageType: number = 0;
        static errorMessageType: number = 1;
        static warningMessageType: number = 2;

        static serviceResultSuccess: number = 0;
        static serviceResultPending: number = 1;
        static serviceResultWarnings: number = 2;
        static serviceResultErrors: number = 3;
        static serviceResultServiceFailure: number = 4;
        static serviceResultServiceNotAvailable: number = 5;

        securityToken: string = '';

        readSecurityToken() {
            var cookie = document.cookie;
            if (cookie) {
                var match = cookie.match(new RegExp('peanutSecurity=([^;]+)'));
                if (match) {
                    return match[1];
                }
            }
            return '';
        }

        parseErrorResult(result: any): string {
            var me = this;
            var errorDetailLevel = 4; // verbosity control to be implemented later
            var responseText = "An unexpected system error occurred.";
            try {
                // WCF returns a big whopping HTML page.  Could add code later to parse it but for now, just status info.
                if (result.status) {
                    if (result.status == '404')
                        return responseText + " The web service was not found.";
                    else {
                        responseText = responseText + " Status: " + result.status;
                        if (result.statusText)
                            responseText = responseText + " " + result.statusText
                    }
                }
            }
            catch (ex) {
                responseText = responseText + " Error handling failed: " + ex.toString;
            }
            return responseText;

        }

        setSecurityToken(token: string) {
            this.securityToken = token;
        }
        getInfoMessages(messages: IServiceMessage[]): string[]{
            var _peanut = this;
            // var me = this;
            var result = new Array<string>();

            var j = 0;
            for (var i = 0; i < messages.length; i++) {
                var message = messages[i];
                if (message.MessageType == Tops.Peanut.infoMessageType)
                    result[j++] = message.Text;
            }

            return result;
        }


        getNonErrorMessages(messages: IServiceMessage[]): string[] {
            var _peanut = this;
            var me = this;
            var result = new Array<string>();

            var j = 0;
            for (var i = 0; i < messages.length; i++) {
                var message = messages[i];
                if (message.MessageType != Tops.Peanut.errorMessageType)
                    result[j++] = message.Text;
            }

            return result;
        }


        getErrorMessages(messages: IServiceMessage[]): string[] {
            var _peanut = this;
            var result = new Array<string>()

            var j = 0;
            for (var i = 0; i < messages.length; i++) {
                var message = messages[i];
                if (message.MessageType == Tops.Peanut.errorMessageType)
                    result[j++] = message.Text;
            }

            return result;
        }


        getMessagesText(messages: IServiceMessage[]): string[] {
            var result = new Array<string>();
            var j = 0;
            for (var i = 0; i < messages.length; i++) {
                var message = messages[i];
                result[j++] = message.Text;
            }
            return result;
        }


        hideServiceMessages(): void {
            var _peanut = this;
            if (_peanut.clientApp.viewModel) {
                var viewModel: any = _peanut.clientApp.viewModel;
                if (typeof (viewModel.hideServiceMessages) !== undefined && viewModel.hideServiceMessages != null) {
                    viewModel.hideServiceMessages();
                    return;
                }
            }

            _peanut.clientApp.hideServiceMessages();
        }

        showServiceMessages(serviceResponse: IServiceResponse): void {
            var _peanut = this;
            if (serviceResponse == null || serviceResponse.Messages == null || serviceResponse.Messages.length == 0)
                return;

            // var vm = _peanut.getCurrentViewModel();
            if (_peanut.clientApp.viewModel) {
                var viewModel: any = _peanut.clientApp.viewModel;

                if (typeof (viewModel.showServiceMessages) !== undefined && viewModel.showServiceMessages != null) {
                    viewModel.showServiceMessages(serviceResponse.Messages);
                    return;
                }
            }

            _peanut.clientApp.showServiceMessages(serviceResponse.Messages);
        }

        handleServiceResponse(serviceResponse: IServiceResponse): boolean {
            var _peanut = this;
            _peanut.showServiceMessages(serviceResponse);
            return true;
        }

        showExceptionMessage(errorResult: any): string {
            var _peanut = this;
            var errorMessage = _peanut.parseErrorResult(errorResult);
            _peanut.clientApp.showError(errorMessage);
            return errorMessage;
        }


        executeRPC(requestMethod: string, serviceName: string, parameters: any = "",
                       successFunction?: (serviceResponse: IServiceResponse) => void,
                       errorFunction?: (errorMessage: string) => void) : JQueryPromise<any> {
            var _peanut = this;

            // peanut controller requires parameter as a string.
            if (!parameters)
                parameters = "";
            else  {
                parameters = JSON.stringify(parameters);
            }
            var serviceRequest = { "serviceCode" : serviceName, "topsSecurityToken": _peanut.securityToken,  "request" : parameters};

            var serviceUrl =  _peanut.clientApp.serviceUrl; // Drupal 8/7: tops/service, Drupal 6 or PHP: 'topsService.php';

            var result =
                jQuery.ajax({
                    type: requestMethod, // "POST",
                    data: serviceRequest,
                    dataType: "json",
                    cache: false,
                    url: serviceUrl
                })
                    .done(
                    function(serviceResponse) {
                        _peanut.showServiceMessages(serviceResponse);
                        if (successFunction) {
                            successFunction(serviceResponse);
                        }
                    }
                )
                    .fail(
                    function(jqXHR, textStatus ) {
                        var errorMessage = _peanut.showExceptionMessage(jqXHR);
                        if (errorFunction)
                            errorFunction(errorMessage);
                    });


            return result;
        }


        // Execute a peanut service and handle Service Response.
        executeService(serviceName: string, parameters: any = "",
                       successFunction?: (serviceResponse: IServiceResponse) => void,
                       errorFunction?: (errorMessage: string) => void) : JQueryPromise<any> {
            var _peanut = this;
            return _peanut.executeRPC("POST", serviceName, parameters, successFunction, errorFunction);
        }

        // GET is no longer supported. This method is for backward compatibility but is identical to execute service
        getFromService(serviceName: string, parameters: any = "",
                       successFunction?: (serviceResponse: IServiceResponse) => void,
                       errorFunction?: (errorMessage: string) => void) : JQueryPromise<any> {
            var _peanut = this;
            return _peanut.executeRPC("POST", serviceName, parameters, successFunction, errorFunction);
        }

        /*
         * Utility routines
         */

        getRequestParam(name){
            if(name=(new RegExp('[?&]'+encodeURIComponent(name)+'=([^&]*)')).exec(location.search))
                return decodeURIComponent(name[1]);
        }

        validateEmail(email: string) {
            if (!email || email.trim() == '') {
                return false;
            }
            return /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email);
        }

    }
}
