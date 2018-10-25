{strip}
	<div class="tpl-Base-ConditionBuilderGroup js-condition-builder-group-container pl-4">
		<div class="btn-group btn-group-toggle js-condition-switch mr-2" data-toggle="buttons">
			<label class="btn btn-sm btn-outline-primary js-condition-switch-value {if $CONDITIONS_GROUP['condition'] eq 'AND' || empty($CONDITIONS_GROUP['condition'])}active {/if}">
				<input type="radio" autocomplete="off">
				AND
			</label>
			<label class="btn btn-sm btn-outline-primary {if $CONDITIONS_GROUP['condition'] eq 'OR'}active {/if}">
				<input type="radio" autocomplete="off">
				OR
			</label>
		</div>
		<div class="btn-group btn-group-toggle">
			<button type="button" class="btn btn-sm btn-success js-condition-add" data-js="click">
				<span class="fa fa-plus mr-1"></span>{\App\Language::translate('LBL_ADD_CONDITION',$MODULE_NAME)}
			</button>
			<button type="button" class="btn btn-sm btn-success js-group-add" data-js="click">
				<span class="fa fa-plus mr-1"></span>{\App\Language::translate('LBL_ADD_CONDITION_GROUP',$MODULE_NAME)}
			</button>
			{if empty($ROOT_ITEM)}
				<button type="button" class="btn btn-sm btn-danger js-group-delete" data-js="click">
					<span class="fa fa-trash"></span>
				</button>
			{/if}
		</div>
		<div class="js-condition-builder-conditions-container">
			{foreach from=$CONDITIONS_GROUP['rules'] item=CONDITION_ITEM}
				{if isset($CONDITION_ITEM['condition'])}
					{include file=\App\Layout::getTemplatePath('ConditionBuilderGroup.tpl', $MODULE_NAME) CONDITIONS_GROUP=$CONDITION_ITEM ROOT_ITEM=false}
				{else}
					{include file=\App\Layout::getTemplatePath('ConditionBuilderRow.tpl', $MODULE_NAME) CONDITIONS_ROW=$CONDITION_ITEM }
				{/if}
			{/foreach}
		</div>
	</div>
{/strip}