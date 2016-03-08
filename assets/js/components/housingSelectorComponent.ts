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
        motelOccupancy : string = '';
        occupancy = ko.observable();
        day : number;
        assignment : IHousingAssignment;
        attenderId = 0;
        note = ko.observable();

        public constructor(params: any) {
            var me = this;
            me.owner = params.owner;
            var attender = params.attender;
            me.assignment = params.assignment;
            me.attenderId = attender.attenderId;
            me.day = me.assignment.day;
            switch(me.assignment.day) {
                case 4: me.dayName('Thursday'); break;
                case 5 : me.dayName('Friday'); break;
                case 6 : me.dayName('Saturday'); break;
                case 7 : me.dayName('Sunday'); break;
                default : me.dayName(''); break;
            }
            me.note(me.assignment.note);
            me.housingTypes = me.owner.housingTypes;
            var housingUnitList =  me.owner.getHousingUnitList(0, me.day);
            var unit = me.owner.getHousingUnit(me.assignment.housingUnitId, housingUnitList);
            var typeId = attender.housingPreference;
            if (unit != null && (unit.housingTypeId)) {
                typeId = unit.housingTypeId;
            }
            me.filterHousingUnitList(typeId);
            var type = me.owner.getHousingType(typeId);
            me.motelOccupancy = attender.occupancy;

            if ((<IHousingTypeLookupItem>type).category == 3) {
                me.occupancy(me.motelOccupancy);
            }
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
                if ((<IHousingTypeLookupItem>selected).category == 3) {
                    me.occupancy(me.motelOccupancy);
                }
                else {
                    me.occupancy('');
                }
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

            var filtered =  me.owner.getHousingUnitList(typeId,me.day);
            me.housingUnits(filtered);
            me.selectedUnit(null);
        }

    }
}

Tops.TkoComponentLoader.addVM('housing-selector',Tops.housingSelectorComponent);
