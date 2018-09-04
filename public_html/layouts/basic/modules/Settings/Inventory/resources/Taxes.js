/* {[The file is published on the basis of YetiForce Public License 3.0 that can be found in the following directory: licenses/LicenseEN.txt or yetiforce.com]} */
// 'use strict';

Settings_Inventory_Index_Js("Settings_Inventory_Taxes_Js", {}, {
	/*
	 * Function to add the Details in the list after saving
	 */
	addDetails: function (details) {
		var container = jQuery('#inventory');
		var currency = jQuery('#currency');
		var symbol = '%';
		if (currency.length > 0) {
			var currency = JSON.parse(currency.val());
			symbol = currency.currency_symbol;
		}
		var table = $('.inventoryTable', container);
		if (details.default === 1) {
			var defaultCheck = ' checked';
			table.find('.default').prop('checked', false);
		}
		let trElement = $(
			`<tr class="opacity" data-id="${details.id}">
				<td class="textAlignCenter ${details.row_type}"><label class="name">${details.name}</label></td>
				<td class="textAlignCenter ${details.row_type}"><span class="value">${details.value} ${symbol}</span></td>
				<td class="textAlignCenter ${details.row_type}"><input class="status js-update-field mt-2" checked type="checkbox"></td>
				<td class="textAlignCenter ${details.row_type}">
					<div class="float-right  w-50 d-flex justify-content-between mr-2">
						<input class="default js-update-field mt-2" ${defaultCheck} data-field-name="default" type="checkbox">
						<div class="actions">
							<button class="btn btn-info btn-sm text-white editInventory u-cursor-pointer" data-url="' + details._editurl + '">
								<span title="Edycja" class="fas fa-edit alignBottom"></span>
							</button>
							<button class="removeInventory u-cursor-pointer btn btn-danger btn-sm text-white" data-url="' + details._editurl + '">
								<span title="Usuń" class="fas fa-trash-alt alignBottom"></span>
							</button>
						</div>
					</div>
				</td>
			</tr>`
		);
		table.append(trElement);
	}
});

