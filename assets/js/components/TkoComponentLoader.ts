/**
 * Created by Terry on 10/29/2015.
 */
/// <reference path='../typings/jquery/jquery.d.ts' />
/// <reference path="../typings/knockout/knockout.d.ts" />
/// <reference path="../typings/custom/head.load.d.ts" />
module Tops {
    export class TkoComponentLoader {
        static instance : TkoComponentLoader;
        components = [];

        constructor(
            private applicationPath = '',
            private htmlPath = 'assets/templates',
            private vmPath = 'assets/js/components') {

            TkoComponentLoader.instance = this;
        }


        public static addVM(name : string, vm : any) {
            TkoComponentLoader.instance.components[name] = vm;
        }

        public  getVM(name : string) : any {
            var me = this;
            return (name in me.components) ? me.components[name] : null;
        }

        private nameToFileName(name: string) {
            var me = this;
            var parts = name.split('-');
            var fileName = parts[0];
            if (parts.length > 1) {
                fileName += parts[1].charAt(0).toUpperCase() + parts[1].substring(1);
            }
            fileName += 'Component';
            return fileName;
        }

        public load(name: string, successFunction : () => void ) {
            var me = this;
            var fileName = me.nameToFileName(name);
            var htmlPath = me.applicationPath + me.htmlPath + '/' + fileName + '.html';
            jQuery.get(htmlPath, function (htmlSource:string) {
                var src = me.applicationPath + me.vmPath + '/' + fileName + '.js';
                head.load(src, function () {
                    var vm = me.getVM(name);
                    if (vm) {
                        ko.components.register(name, {
                            viewModel: vm, //  {instance: vm}, // testComponentVm,
                            template: htmlSource
                        });
                    }
                    if (successFunction) {
                        successFunction();
                    }
                });
            });
        }

        public loadInstance(name: string, vmInstance: any, successFunction : () => void ) {
            var me = this;
            var fileName = me.nameToFileName(name);
            var htmlPath = me.applicationPath + me.htmlPath + '/' + fileName + '.html';
            jQuery.get(htmlPath, function (htmlSource:string) {
                ko.components.register(name, {
                    viewModel: {instance: vmInstance},
                    template: htmlSource
                });
                if (successFunction) {
                    successFunction();
                }
            });
        }
    }
}

// Tops.TkoComponentLoader.instance = new Tops.TkoComponentLoader();
// (<any>window).TkoComponents = Tops.TkoComponentLoader.instance;