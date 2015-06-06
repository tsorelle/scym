/**
 * Created by Terry on 3/3/2015.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
module Tops {

    export class MailboxesViewModel implements IMainViewModel {
        static instance: Tops.MailboxesViewModel;
        private application: Tops.Application;
        private peanut: Tops.Peanut;

        private editModal : any;

        private insertId: number = 0;

        private tempMailbox : mailBox;

        // Constructor
        constructor() {
            var me = this;
            Tops.MailboxesViewModel.instance = me;
            me.application = new Tops.Application(me);
            me.peanut = me.application.peanut;
        }

        // observables
        mailboxList : KnockoutObservableArray<mailBox> = ko.observableArray([]);
        updatedMailboxList : KnockoutObservableArray<mailBox> = ko.observableArray([]);
        mailboxId = ko.observable('');
        mailboxCode = ko.observable('');
        mailboxName = ko.observable('');
        mailboxDescription = ko.observable('');
        mailboxEmail = ko.observable('');
        mailboxState = ko.observable(0);

        formHeading = ko.observable('');
        editMode  = ko.observable('');

        mailboxDescriptionHasError = ko.observable(false);//false
        mailboxEmailHasError = ko.observable(false);//false
        mailboxNameHasError = ko.observable(false);//false
        mailboxCodeHasError = ko.observable(false);//false


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
                    me.application.showWaiter('Getting mailbox list. Please wait...');
                    me.getMailboxList(successFunction);
                }
            );
        }


        hideForm() {
            jQuery("#mailbox-update-modal").modal('hide');
        }

        showForm() {
            var me = this;
            me.clearValidation();
            jQuery("#mailbox-update-modal").modal('show');
        }

        hideConfirmForm() {
            jQuery("#confirm-delete-modal").modal('hide');
        }

        showConfirmForm() {
            var me = this;
            jQuery("#confirm-delete-modal").modal('show');
        }

        // event handlers
        getMailboxList(doneFunction?: () => void) {
            var me = this;
            //me.application.showWaiter('Please wait...');
            me.peanut.getFromService( 'mailboxes.GetMailboxList',null, function (serviceResponse: Tops.IServiceResponse) {
                    if (serviceResponse.Result == Tops.Peanut.serviceResultSuccess) {
                        var list = <mailBox[]>serviceResponse.Value;
                        me.mailboxList(_.sortBy(list,function(box) {return box.name.toLowerCase()}));

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
            me.clearValidation();
            me.editMode('update');
            me.mailboxId(box.id);
            me.mailboxCode(box.code);
            me.mailboxName(box.name);
            me.mailboxEmail(box.email);
            me.mailboxDescription(box.description);
            me.formHeading("Edit mailbox: " + box.code);
            me.mailboxState(2);
            me.showForm();
        };

        getMailbox() {
            // GetMailbox
        }

        newMailbox() {
            var me = this;
            me.clearValidation();
            me.editMode('add');
            me.mailboxId('0');
            me.mailboxCode('');
            me.mailboxName('');
            me.mailboxEmail('');
            me.mailboxDescription('');
            me.formHeading('New mailbox');
            me.mailboxState(1);
            me.showForm();
        }



        clearValidation() {
            var me = this;
            me.mailboxCodeHasError(false);
            me.mailboxDescriptionHasError(false);
            me.mailboxEmailHasError(false);
            me.mailboxDescriptionHasError(false);
            me.mailboxNameHasError(false);
        }

        createMailboxDto() {
            var me = this;
            var valid = true;
            var box = new mailBox();
            box.id = me.mailboxId();
            box.code = me.mailboxCode();
            box.name = me.mailboxName();
            box.email = me.mailboxEmail();
            box.state = me.mailboxState();
            box.description = me.mailboxDescription();

            if (box.code == '') {
                me.mailboxCodeHasError(true);
                valid = false;
            }
            if (box.name == '') {
                me.mailboxNameHasError(true);
                valid = false;
            }
            /*
            if (box.description == '') {
                me.mailboxDescriptionHasError(true);
                valid = false;
            }
            */

            var emailOk = me.peanut.validateEmail(box.email);
            me.mailboxEmailHasError(!emailOk);
            if (!emailOk) {
                valid = false;
                me.mailboxEmailHasError(true);
            }
            if (valid) {
                return box;
            }
            return null;
        }

        updateMailbox() {
            // UpdateMailbox
            var me = this;
            var box = me.createMailboxDto();
            if (box) {
                me.submitChanges(box);
            }
        }


        confirmRemoveMailbox = (box : mailBox)=> {
            var me = this;
            box.state = 3;
            me.tempMailbox = box;
            me.mailboxCode(box.code);
            me.showConfirmForm();
        };

        removeMailbox() {
            var me = this;
            me.hideConfirmForm();
            me.submitChanges(me.tempMailbox);
        }


        submitChanges(box: mailBox) {
            var me = this;
            me.hideForm();
            me.application.showWaiter('Updating mailboxes. Please wait...');
            me.peanut.executeService('mailboxes.UpdateMailbox', box, function (serviceResponse:Tops.IServiceResponse) {
                me.application.hideWaiter();
                if (serviceResponse.Result == Tops.Peanut.serviceResultSuccess) {
                    me.hideForm();
                    var list = <mailBox[]>serviceResponse.Value;
                    me.mailboxList(_.sortBy(list,function(box) {return box.name.toLowerCase()}));
                }
            }).fail(function () {
                me.application.hideWaiter();
            });

        }
    }
}

Tops.MailboxesViewModel.instance = new Tops.MailboxesViewModel();
(<any>window).ViewModel = Tops.MailboxesViewModel.instance;