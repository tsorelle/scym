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


        public static addVM(componentName : string, vm : any) {
            TkoComponentLoader.instance.components[componentName] = vm;
        }

        public  getVM(componentName : string) : any {
            var me = this;
            return (componentName in me.components) ? me.components[componentName] : null;
        }

        private nameToFileName(componentName: string) {
            var me = this;
            var parts = componentName.split('-');
            var fileName = parts[0];
            if (parts.length > 1) {
                fileName += parts[1].charAt(0).toUpperCase() + parts[1].substring(1);
            }
            // fileName += 'Component';
            return fileName;
        }

        public alreadyLoaded(componentName:string) : boolean {
            var me = this;
            return (componentName in me.components)
        }

        public load(componentName: string, finalFunction : () => void ) {
            var me = this;
            if (componentName in me.components) {
                // don't double load.
                if (finalFunction) {
                    finalFunction();
                }
                return;
            }
            var fileName = me.nameToFileName(componentName);
            var htmlPath = me.applicationPath + me.htmlPath + '/' + fileName + '.html';

            jQuery.get(htmlPath, function (htmlSource:string) {
                var src = me.applicationPath + me.vmPath + '/' + fileName + 'Component.js';
                head.load(src, function () {
                    var vm = me.getVM(componentName);
                    if (vm) {
                        ko.components.register(componentName, {
                            viewModel: vm, //  {instance: vm}, // testComponentVm,
                            template: htmlSource
                        });
                    }
                    if (finalFunction) {
                        finalFunction();
                    }
                });
            });
        }

        // assumes component source already losded
        public registerComponent(
                            componentName: string,
                            vm : any,
                            finalFunction : (vmInstance?:any) => void ) {

            var me = this;
            var fileName = me.nameToFileName(componentName);
            var htmlPath = me.applicationPath + me.htmlPath + '/' + fileName + '.html';
            jQuery.get(htmlPath, function (htmlSource:string) {
                // vmInstance can be a function returning the instance or the instance itself
                if (vm) {
                    ko.components.register(componentName, {
                        viewModel: {instance: vm},
                        template: htmlSource
                    });
                }
                if (finalFunction) {
                    finalFunction(vm);
                }
            });
        }

        public loadComponentInstance(name: string,
                            vmInstance : any, //  getVmInstance : () => any,
                            finalFunction : (vmInstance?:any) => void ) {
            var me = this;
            var fileName = me.nameToFileName(name);
            var htmlPath = me.applicationPath + me.htmlPath + '/' + fileName + '.html';

            jQuery.get(htmlPath, function (htmlSource:string) {
                var src = me.applicationPath + me.vmPath + '/' + fileName + 'Component.js';
                head.load(src, function () {
                    // vmInstance can be a function returning the instance or the instance itself
                    var vm = (jQuery.isFunction(vmInstance)) ? vmInstance() :  vmInstance;

                    if (vm) {
                        ko.components.register(name, {
                            viewModel: {instance: vm},
                            template: htmlSource
                        });
                    }
                    if (finalFunction) {
                        finalFunction(vm);
                    }
                });
            });
        }
    }
}

// Tops.TkoComponentLoader.instance = new Tops.TkoComponentLoader();
// (<any>window).TkoComponents = Tops.TkoComponentLoader.instance;