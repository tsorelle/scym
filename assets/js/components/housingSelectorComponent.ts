/**
 * Created by Terry on 12/26/2015.
 */
///<reference path="../Tops.App/registration.d.ts"/>
///<reference path="../typings/underscore/underscore.d.ts"/>
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
///<reference path="../Tops.Peanut/Peanut.ts"/>
/// <reference path='TkoComponentLoader.ts' />
module Tops {
    export class housingSelectorComponent {

        housingTypes : KnockoutObservableArray<ILookupItem>;
        housingUnitList: IHousingUnit[];
        housingUnits =  ko.observableArray<IHousingUnit>();
        selectedUnit = ko.observable<IHousingUnit>();
        selectedType = ko.observable<ILookupItem>();
        owner: IHousingViewModel;
        dayName = ko.observable();
        assignment : IHousingAssignment;
        attenderId = 0;
        note = ko.observable();

        public constructor(params: any) {
            var me = this;
            me.owner = params.owner;
            var attender = params.attender;
            me.assignment = params.assignment;
            me.attenderId = attender.attenderId;
            switch(me.assignment.day) {
                case 4: me.dayName('Thursday'); break;
                case 5 : me.dayName('Friday'); break;
                case 6 : me.dayName('Saturday'); break;
                case 7 : me.dayName('Sunday'); break;
                default : me.dayName(''); break;
            }
            me.note(me.assignment.note);
            me.housingTypes = me.owner.housingTypes;
            var unit = me.owner.getHousingUnit(me.assignment.housingUnitId);
            var typeId = unit.housingTypeId ? unit.housingTypeId : attender.housingPreference;
            me.filterHousingUnitList(typeId);
            var type = me.owner.getHousingType(typeId);
            me.selectedType(type);
            me.selectedUnit(unit);
            me.selectedType.subscribe(me.onTypeChange);
            me.selectedUnit.subscribe(me.onUnitChange);
            me.note.subscribe(me.onNoteChange);
        }

        onTypeChange = (selected : ILookupItem) => {
            var me = this;
            var id = 0;
            if (selected) {
                id = selected.Key;
            }
            me.filterHousingUnitList(id);

        };

        onUnitChange = (selected: IHousingUnit) => {
            var me = this;
            if (selected) {
              me.assignment.housingUnitId =  selected.housingUnitId;
            }
            else {
                me.assignment.housingUnitId = 0;
            }
            me.owner.updateAssignment(me.attenderId,me.assignment);
        };

        onNoteChange = (note: any) => {
            var me = this;
            me.assignment.note = note;
            me.owner.updateAssignment(me.attenderId,me.assignment);
        };

        private filterHousingUnitList(typeId: number) {
            var me = this;

            var filtered = me.owner.getHousingUnitList(typeId);
            me.housingUnits(filtered);
            me.selectedUnit(null);
        }

        /*

        private getHousingUnit(id: number) {
            var me = this;
            var selected = null;
            if (id > 0) {
                selected = _.find( me.housingUnitList, function (unit:IHousingUnit) {
                    return unit.housingUnitId == id;
                }, me);
            }
            return selected;
        }

        private getHousingType(id: number) {
            var me = this;
            var selected = null;
            if (id > 0) {
                selected = _.find( me.housingTypes(), function (item:ILookupItem) {
                    return item.Key == id;
                }, me);
            }
            return selected;
        }
         */

    }
}

Tops.TkoComponentLoader.addVM('housing-selector',Tops.housingSelectorComponent);
