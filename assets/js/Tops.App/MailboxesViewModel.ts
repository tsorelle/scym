/**
 * Created by Terry on 3/3/2015.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />

module Tops {

    export class mailBox {
        id: string = '';
        name: string = '';
        description: string = '';
        code: string = '';
        email: string = '';
    }

    export class MailboxesViewModel implements IMainViewModel {
        static instance: Tops.MailboxesViewModel;
        private application: Tops.Application;
        private peanut: Tops.Peanut;

        // Constructor
        constructor() {
            var me = this;
            Tops.MailboxesViewModel.instance = me;
            me.application = new Tops.Application(me);
            me.peanut = me.application.peanut;
        }

        /**
         * @param applicationPath - root path of application or location of service script
         * @param successFunction - page inittializations such as ko.applyBindings() go here.
         *
         * Call this function in a script at the end of the page following the closing "body" tag.
         * e.g.
         *      ViewModel.init('/', function() {
         *          ko.applyBindings(ViewModel);
         *      });
         *
         */
        init(applicationPath: string, successFunction?: () => void) {
            var me = this;
            // setup messaging and other application initializations
            me.application.initialize(applicationPath,
                function() {
                    // do view model initializations here.
                    me.getMailboxList(successFunction);
                }
            );
        }

        // observables
        mailboxList : KnockoutObservableArray<mailBox> = ko.observableArray([]);

        mailboxId = ko.observable('');
        mailboxCode = ko.observable('');
        mailboxName = ko.observable('');
        mailboxDescription = ko.observable('');
        mailboxEmail = ko.observable('');
        showList = ko.observable(false);
        showForm = ko.observable(false);
        formHeading = ko.observable('');

        selectListView() {
            var me = this;
            me.showList(true);
            me.showForm(false);
        }

        selectFormView() {
            var me = this;
            me.showList(false);
            me.showForm(true);
        }

        // event handlers
        getMailboxList(doneFunction?: () => void) {
            var me = this;
            me.application.showWaiter('Please wait...');
            me.peanut.getFromService( 'mailboxes.GetMailboxList',null, function (serviceResponse: Tops.IServiceResponse) {
                    if (serviceResponse.Result == Tops.Peanut.serviceResultSuccess) {
                        var list = <mailBox[]>serviceResponse.Value;
                        me.mailboxList(list);
                        me.selectListView();
                    }
                    else {
                        // alert("Service failed");
                    }
                }
            ).always(function() {
                    me.application.hideWaiter();
                    if (doneFunction) {
                        doneFunction();
                    }
                });
        }

        editMailbox = (box: mailBox) => {
            var me = this;
            me.mailboxId(box.id);
            me.mailboxCode(box.code);
            me.mailboxName(box.name);
            me.mailboxEmail(box.email);
            me.mailboxDescription(box.description);
            me.formHeading("Edit mailbox: " + box.code);
            me.selectFormView();
        };

        getMailbox() {
            // GetMailbox
        }

        newMailbox() {
            var me = this;
            me.mailboxId('0');
            me.mailboxCode('');
            me.mailboxName('');
            me.mailboxEmail('');
            me.mailboxDescription('');
            me.formHeading('New mailbox');
            me.selectFormView();
        }

        updateMailbox() {
            // UpdateMailbox
            var me = this;
            var box = new mailBox();
            box.id = me.mailboxId();
            box.code = me.mailboxCode();
            box.name = me.mailboxName();
            box.email = me.mailboxEmail();
            box.description = me.mailboxDescription();
            me.application.showWaiter('Please wait...');
            me.peanut.executeService( 'mailboxes.UpdateMailbox',box, function(serviceResponse: Tops.IServiceResponse) {
                me.application.hideWaiter();
                if (serviceResponse.Result == Tops.Peanut.serviceResultSuccess) {
                    me.getMailboxList();
                }
            }).fail(function() {
                  me.application.hideWaiter();
            });


        }

        returnToList() {

        }
    }
}

Tops.MailboxesViewModel.instance = new Tops.MailboxesViewModel();
(<any>window).ViewModel = Tops.MailboxesViewModel.instance;