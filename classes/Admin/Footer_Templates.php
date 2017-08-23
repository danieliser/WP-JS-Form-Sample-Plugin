<?php
/**
 * @copyright   Copyright (c) 2017, Code Atlantic
 * @author      Daniel Iser
 */

namespace WPJSFSP\Admin;

use WPJSFSP\Schedules;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Footer_Templates {

	public static function render() {
		self::general_fields();
		self::html5_fields();
		self::custom_fields();
		self::misc_fields();
		self::helpers();
		self::conditions_editor();
	}

	public static function general_fields() {
		?>
		<script type="text/html" id="tmpl-wpjsfsp-field-text">
			<input type="text" placeholder="{{data.placeholder}}" class="{{data.size}}-text" id="{{data.id}}" name="{{data.name}}" value="{{data.value}}" {{{data.meta}}} />
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-password">
			<input type="password" placeholder="{{data.placeholder}}" class="{{data.size}}-text" id="{{data.id}}" name="{{data.name}}" value="{{data.value}}" {{{data.meta}}} />
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-select">
			<select id="{{data.id}}" name="{{data.name}}" data-allow-clear="true" {{{data.meta}}}>
				<# _.each(data.options, function(option, key) {

					if (option.options !== undefined && option.options.length) { #>

					<optgroup label="{{{option.label}}}">

						<# _.each(option.options, function(option, key) { #>
							<option value="{{option.value}}" {{{option.meta}}}>{{option.label}}</option>
							<# }); #>

					</optgroup>

					<# } else { #>
						<option value="{{option.value}}" {{{option.meta}}}>{{{option.label}}}</option>
						<# }

							}); #>
			</select>
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-radio">
			<ul class="wpjsfsp-field-radio-list">
				<# _.each(data.options, function(option, key) { #>
					<li <# print(option.value === data.value ? 'class="wpjsfsp-selected"' : ''); #>>
						<input type="radio" id="{{data.id}}_{{key}}" name="{{data.name}}" value="{{option.value}}" {{{option.meta}}} />
						<label for="{{data.id}}_{{key}}">{{{option.label}}}</label>
					</li>
					<# }); #>
			</ul>
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-checkbox">
			<input type="checkbox" id="{{data.id}}" name="{{data.name}}" value="1" {{{data.meta}}} />
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-multicheck">
			<ul class="wpjsfsp-field-mulitcheck-list">
				<# _.each(data.options, function(option, key) { #>
					<li>
						<input type="checkbox" id="{{data.id}}_{{key}}" name="{{data.name}}[{{option.value}}]" value="1" {{{option.meta}}} />
						<label for="{{data.id}}_{{key}}">{{option.label}}</label>
					</li>
				<# }); #>
			</ul>
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-textarea">
			<textarea name="{{data.name}}" id="{{data.id}}" class="{{data.size}}-text" {{{data.meta}}}>{{data.value}}</textarea>
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-hidden">
			<input type="hidden" class="{{data.classes}}" id="{{data.id}}" name="{{data.name}}" value="{{data.value}}" {{{data.meta}}} />
		</script>
		<?php
	}

	public static function html5_fields() {
		?>
		<script type="text/html" id="tmpl-wpjsfsp-field-range">
			<input type="range" placeholder="{{data.placeholder}}" class="{{data.size}}-text" id="{{data.id}}" name="{{data.name}}" value="{{data.value}}" {{{data.meta}}} />
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-search">
			<input type="search" placeholder="{{data.placeholder}}" class="{{data.size}}-text" id="{{data.id}}" name="{{data.name}}" value="{{data.value}}" {{{data.meta}}} />
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-number">
			<input type="number" placeholder="{{data.placeholder}}" class="{{data.size}}-text" id="{{data.id}}" name="{{data.name}}" value="{{data.value}}" {{{data.meta}}} />
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-email">
			<input type="email" placeholder="{{data.placeholder}}" class="{{data.size}}-text" id="{{data.id}}" name="{{data.name}}" value="{{data.value}}" {{{data.meta}}} />
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-url">
			<input type="url" placeholder="{{data.placeholder}}" class="{{data.size}}-text" id="{{data.id}}" name="{{data.name}}" value="{{data.value}}" {{{data.meta}}} />
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-tel">
			<input type="tel" placeholder="{{data.placeholder}}" class="{{data.size}}-text" id="{{data.id}}" name="{{data.name}}" value="{{data.value}}" {{{data.meta}}} />
		</script>
		<?php
	}

	public static function custom_fields() {
		?>
		<script type="text/html" id="tmpl-wpjsfsp-field-editor">
			<textarea name="{{data.name}}" id="{{data.id}}" class="wpjsfsp-wpeditor {{data.size}}-text" {{{data.meta}}}>{{data.value}}</textarea>
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-link">
			<button type="button" class="dashicons dashicons-admin-generic button"></button>            <input type="text" placeholder="{{data.placeholder}}" class="{{data.size}}-text" id="{{data.id}}" name="{{data.name}}" value="{{data.value}}" {{{data.meta}}} />
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-rangeslider">
			<input type="text" id="{{data.id}}" name="{{data.name}}" value="{{data.value}}" class="wpjsfsp-range-manual" {{{data.meta}}} />
			<span class="wpjsfsp-range-value-unit regular-text">{{data.unit}}</span>
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-color">
			<input type="text" class="wpjsfsp-color-picker color-picker" id="{{data.id}}" name="{{data.name}}" value="{{data.value}}" data-default-color="{{data.std}}" {{{data.meta}}} />
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-measure">
			<input type="number" id="{{data.id}}" name="{{data.name}}" value="{{data.value}}" size="5" {{{data.meta}}} />            <select id="{{data.id}}_unit" name="<# print(data.name.replace(data.id, data.id + '_unit')); #>">
				<# _.each(data.units, function(option, key) { #>
					<option value="{{option.value}}" {{{option.meta}}}>{{{option.label}}}</option>
					<# }); #>
			</select>
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-license_key">
			<input class="{{data.size}}-text" id="{{data.id}}" name="{{data.name}}" value="{{data.value.key}}" {{{data.meta}}} />

			<# if (data.value.key !== '') { #>
				<?php wp_nonce_field( 'wpjsfsp_license_activation', 'wpjsfsp_license_activation_nonce' ); ?>
				<# if (data.value.status === 'valid') { #>
					<span class="wpjsfsp-license-status"><?php _e( 'Active', 'wp-js-form-sample-plugin' ); ?></span>
					<input type="submit" class="button-secondary wpjsfsp-license-deactivate" id="{{data.id}}_deactivate" name="wpjsfsp_license_deactivate[{{data.id}}]" value="<?php _e( 'Deactivate License', 'wp-js-form-sample-plugin' ); ?>" />
				<# } else { #>
					<span class="wpjsfsp-license-status"><?php _e( 'Inactive', 'wp-js-form-sample-plugin' ); ?></span>
					<input type="submit" class="button-secondary wpjsfsp-license-activate" id="{{data.id}}_activate" name="wpjsfsp_license_activate[{{data.id}}]" value="<?php _e( 'Activate License', 'wp-js-form-sample-plugin' ); ?>" />
				<# } #>
			<# } #>

			<# if (data.value.messages && data.value.messages.length) { #>
				<div class="wpjsfsp-license-messages">
					<# for(var i=0; i < data.value.messages.length; i++) { #>
						<p>{{{data.value.messages[i]}}}</p>
					<# } #>
				</div>
			<# } #>
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-datetime">
			<div class="wpjsfsp-datetime">
				<input placeholder="{{data.placeholder}}" data-input class="{{data.size}}-text" id="{{data.id}}" name="{{data.name}}" value="{{data.value}}" {{{data.meta}}} />
				<a class="input-button" data-toggle><i class="dashicons dashicons-calendar-alt"></i></a>
			</div>
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-datetimerange">
			<div class="wpjsfsp-datetime-range">
				<input placeholder="{{data.placeholder}}" data-input class="{{data.size}}-text" id="{{data.id}}" name="{{data.name}}" value="{{data.value}}" {{{data.meta}}} />
				<a class="input-button" data-toggle><i class="dashicons dashicons-calendar-alt"></i></a>
			</div>
		</script>
		<?php
	}

	public static function misc_fields() {
		?>
		<script type="text/html" id="tmpl-wpjsfsp-field-section">
			<div class="wpjsfsp-field-section {{data.classes}}">
				<# _.each(data.fields, function(field) { #>
					{{{field}}}
				<# }); #>
			</div>
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-wrapper">
			<div class="wpjsfsp-field wpjsfsp-field-{{data.type}} {{data.id}}-wrapper {{data.classes}}" data-id="{{data.id}}" <# print( data.dependencies !== '' ? "data-wpjsfsp-dependencies='" + data.dependencies + "'" : ''); #> <# print( data.dynamic_desc !== '' ? "data-wpjsfsp-dynamic-desc='" + data.dynamic_desc + "'" : ''); #>>
				<# if (typeof data.label === 'string' && data.label.length > 0) { #>
					<label for="{{data.id}}">
						{{{data.label}}}
						<# if (data.doclink !== '') { #>
							<a href="{{data.doclink}}" title="<?php _e( 'Documentation', 'wp-js-form-sample-plugin' ); ?>: {{data.label}}" target="_blank" class="wpjsfsp-doclink dashicons dashicons-editor-help"></a>
						<# } #>
					</label>
				<# } else { #>
						<# if (data.doclink !== '') { #>
							<a href="{{data.doclink}}" title="<?php _e( 'Documentation', 'wp-js-form-sample-plugin' ); ?>: {{data.label}}" target="_blank" class="wpjsfsp-doclink dashicons dashicons-editor-help"></a>
						<# } #>
				<# } #>
				{{{data.field}}}
				<# if (typeof data.desc === 'string' && data.desc.length > 0) { #>
					<span class="wpjsfsp-desc desc">{{{data.desc}}}</span>
				<# } #>
			</div>
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-html">
			{{{data.content}}}
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-heading">
			<h3 class="wpjsfsp-field-heading">{{data.desc}}</h3>
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-field-separator">
			<# if (typeof data.desc === 'string' && data.desc.length > 0 && data.desc_position === 'top') { #>
				<h3 class="wpjsfsp-field-heading">{{data.desc}}</h3>
			<# } #>
			<hr {{{data.meta}}} />
			<# if (typeof data.desc === 'string' && data.desc.length > 0 && data.desc_position === 'bottom') { #>
				<h3 class="wpjsfsp-field-heading">{{data.desc}}</h3>
			<# } #>
		</script>
		<?php
	}

	public static function helpers() {
		?>
		<script type="text/html" id="tmpl-wpjsfsp-modal">
			<div id="{{data.id}}" class="wpjsfsp-modal-background {{data.classes}}" role="dialog" aria-hidden="true" aria-labelledby="{{data.id}}-title" aria-describedby="{{data.id}}-description" {{{data.meta}}}>
				<div class="wpjsfsp-modal-wrap">
					<form class="wpjsfsp-form">
						<div class="wpjsfsp-modal-header">
							<# if (data.title.length) { #>
								<span id="{{data.id}}-title" class="wpjsfsp-modal-title">{{data.title}}</span>
							<# } #>
							<button type="button" class="wpjsfsp-modal-close" aria-label="<?php _e( 'Close', 'wp-js-form-sample-plugin' ); ?>"></button>
						</div>

						<# if (data.description.length) { #>
							<span id="{{data.id}}-description" class="screen-reader-text">{{data.description}}</span>
						<# } #>

						<div class="wpjsfsp-modal-content">
							{{{data.content}}}
						</div>

						<# if (data.save_button || data.cancel_button) { #>
							<div class="wpjsfsp-modal-footer submitbox">

								<# if (data.cancel_button) { #>
									<div class="cancel">
										<button type="button" class="submitdelete no-button" href="#">{{data.cancel_button}}</button>
									</div>
								<# } #>

								<# if (data.save_button) { #>
									<div class="wpjsfsp-submit">
										<span class="spinner"></span>
										<button class="button button-primary">{{data.save_button}}</button>
									</div>
								<# } #>

							</div>
						<# } #>
					</form>
				</div>
			</div>
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-tabs">
			<div class="wpjsfsp-tabs-container {{data.classes}}" {{{data.meta}}}>
				<ul class="tabs">
					<# _.each(data.tabs, function(tab, key) { #>
						<li class="tab">
							<a href="#{{data.id + '_' + key}}">{{tab.label}}</a>
						</li>
					<# }); #>
				</ul>

				<# _.each(data.tabs, function(tab, key) { #>
					<div id="{{data.id + '_' + key}}" class="tab-content">
						{{{tab.content}}}
					</div>
				<# }); #>
			</div>
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-shortcode">
			[{{{data.tag}}} {{{data.meta}}}]
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-shortcode-w-content">
			[{{{data.tag}}} {{{data.meta}}}]{{{data.content}}}[/{{{data.tag}}}]
		</script>
		<?php
	}

	public static function conditions_editor() {
		?>
		<script type="text/html" id="tmpl-wpjsfsp-field-conditions">
			<# print(WPJSFSP.conditions.template.editor({groups: data.value})); #>
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-condition-editor">
			<div class="facet-builder <# if (data.groups && data.groups.length) { print('has-conditions'); } #>">
				<p>
					<strong>
						<?php _e( 'These conditions determine when this message will be display.', 'wp-js-form-sample-plugin' ); ?><?php //printf( '%2$s<i class="dashicons dashicons-editor-help" title="%1$s"></i>%3$s', __( 'Learn more about conditions', 'wp-js-form-sample-plugin' ), '<a href="http://docs.usewpjsfsp.com/article/140-conditions" target="_blank">', '</a>' ); ?>
					</strong>
				</p>

				<p><?php _e( 'When users visit your site, the plugin will check the viewed content against your selection below and determine if this message should be shown.', 'wp-js-form-sample-plugin' ); ?></p>


				<section class="wpjsfsp-alert-box" style="display:none"></section>
				<div class="facet-groups condition-groups">
					<#
						_.each(data.groups, function (group, group_ID) {
						print(WPJSFSP.conditions.template.group({
						index: group_ID,
						facets: group
						}));
						});
						#>
				</div>
				<div class="no-facet-groups">
					<label for="wpjsfsp-first-condition"><?php _e( 'Choose a condition to get started.', 'wp-js-form-sample-plugin' ); ?></label>
					<div class="wpjsfsp-field select wpjsfsp-field-select2 facet-target">
						<button type="button" class="wpjsfsp-not-operand" aria-label="<?php _e( 'Enable the Not Operand', 'wp-js-form-sample-plugin' ); ?>">
							<span class="is"><?php _e( 'Is', 'wp-js-form-sample-plugin' ); ?></span>
							<span class="not"><?php _e( 'Is Not', 'wp-js-form-sample-plugin' ); ?></span>
							<input type="checkbox" id="wpjsfsp-first-facet-operand" value="1" />
						</button>
						<# print(WPJSFSP.conditions.template.selectbox({id: 'wpjsfsp-first-condition', name: "", placeholder: "<?php _e( 'Choose a condition to get started.', 'wp-js-form-sample-plugin' ); ?>"})); #>
					</div>
				</div>
			</div>
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-condition-group">

			<div class="facet-group-wrap" data-index="{{data.index}}">
				<section class="facet-group">
					<div class="facet-list">
						<#
							_.each(data.facets, function (facet) {
							print(WPJSFSP.conditions.template.facet(facet));
							});
							#>
					</div>
					<div class="add-or">
						<button type="button" class="add add-facet no-button" aria-label="<?php _ex( 'Add another OR condition', 'aria-label for add new OR condition button', 'wp-js-form-sample-plugin' ); ?>"><?php _e( 'or', 'wp-js-form-sample-plugin' ); ?></button>
					</div>
				</section>
				<p class="and">
					<button type="button" class="add-facet no-button" aria-label="<?php _ex( 'Add another AND condition group', 'aria-label for add new AND condition button', 'wp-js-form-sample-plugin' ); ?>"><?php _e( 'and', 'wp-js-form-sample-plugin' ); ?></button>
				</p>
			</div>
		</script>

		<script type="text/html" id="tmpl-wpjsfsp-condition-facet">
			<div class="facet" data-index="{{data.index}}" data-target="{{data.target}}">
				<i class="or"><?php _e( 'or', 'wp-js-form-sample-plugin' ); ?></i>
				<div class="facet-col facet-target wpjsfsp-field wpjsfsp-field-select wpjsfsp-field-select <# if (typeof data.not_operand !== 'undefined' && data.not_operand == '1') print('not-operand-checked'); #>">
					<button type="button" class="wpjsfsp-not-operand" aria-label="<?php _e( 'Enable the Not Operand', 'wp-js-form-sample-plugin' ); ?>">
						<span class="is"><?php _e( 'Is', 'wp-js-form-sample-plugin' ); ?></span>
						<span class="not"><?php _e( 'Is Not', 'wp-js-form-sample-plugin' ); ?></span>
						<input type="checkbox" name="wpjsfsp_settings[conditions][{{data.group}}][{{data.index}}][not_operand]" value="1"
						<# if (typeof data.not_operand !== 'undefined') print(WPJSFSP.utils.checked(data.not_operand, true, true)); #> />
					</button>
					<# print(WPJSFSP.conditions.template.selectbox({index: data.index, group: data.group, value: data.target, placeholder: "<?php _e( 'Choose a condition', 'wp-js-form-sample-plugin' ); ?>"})); #>
				</div>

				<div class="facet-settings facet-col">
					<#
						print(WPJSFSP.conditions.template.settings(data, data.settings));
						#>
				</div>

				<div class="facet-actions">
					<button type="button" class="remove remove-facet dashicons dashicons-dismiss no-button" aria-label="<?php _e( 'Remove Condition', 'wp-js-form-sample-plugin' ); ?>"></button>
				</div>
			</div>
		</script>
		<?php
	}

}
