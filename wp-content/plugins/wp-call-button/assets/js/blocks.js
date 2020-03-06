/* WP Call Button Block */
( function( blocks, editor, element, components ) {
		var el = element.createElement;
		var RichText = editor.RichText;
		var InspectorControls = editor.InspectorControls;
		var ColorPalette = editor.ColorPalette;
		var BlockControls = editor.BlockControls;
		var CheckboxControl = components.CheckboxControl;
		var FontSizePicker = components.FontSizePicker;

		blocks.registerBlockType( 'wp-call-button/wp-call-button-block', {
				title: wpcallbtn_block_vars.plugin_name,
				icon: 'phone',
				category: 'common',
				attributes: {
						btn_text: {
								type: 'string',
						},
						btn_color: {
								type: 'string',
								default: '#269041',
						},
						btn_txt_color: {
								type: 'string',
								default: '#fff',
						},
						hide_phone_icon: {
								type: 'boolean',
								default: false
						},
						class_for_call_btn: {
								type: 'string',
								default: 'wp-call-button-block-button wp-call-button-block-button-normal'
						},
						btn_font_size : {
								type: 'number',
								default: 16
						},
						btn_center_align: {
								type: 'boolean',
								default: false
						}
				},

				edit: function( props ) {
						var btn_text = props.attributes.btn_text || wpcallbtn_block_vars.data_call_btn_text;
						var btn_color = props.attributes.btn_color || '#269041';
						var btn_txt_color = props.attributes.btn_txt_color || '#fff';
						var hide_phone_icon = props.attributes.hide_phone_icon || false;
						var btn_center_align = props.attributes.btn_center_align || false;
						var btn_font_size = props.attributes.btn_font_size || 16,
								fallbackFontSize = 16;
						var class_for_call_btn = props.attributes.class_for_call_btn;
						if ( class_for_call_btn.indexOf( '-normal' ) === -1 && class_for_call_btn.indexOf( '-center' ) === -1 ) {
							class_for_call_btn = class_for_call_btn + ( btn_center_align ? ' wp-call-button-block-button-center' : ' wp-call-button-block-button-normal' );
						}
						var btnColorsPalette = [
							{ name: 'Green', color: '#269041' },
							{ name: 'Blue', color: '#12A5F4' },
							{ name: 'Red', color: 'red' },
							{ name: 'Yellow', color: 'yellow' },
							{ name: 'Silver', color: 'silver' },
							{ name: 'Gray', color: 'gray' },
							{ name: 'Black', color: 'black' }
						]
						var txtColorsInPalette = [
							{ name: 'white', color: '#fff' },
							{ name: 'black', color: '#000' }
						];
						var fontSizes = [
							{ name: 'Small', slug: 'small', size: 16 },
							{ name: 'Big', slug: 'big', size: 24}
						];	

						function onChangeContent( newContent ) {
								props.setAttributes( { btn_text: newContent } );
						}

						function onBtnTextColorChange( changes ) {
								props.setAttributes( { btn_txt_color: changes } );
						}

						function onBtnColorChange( changes ) {
								props.setAttributes( { btn_color: changes } );
						}
					
						function onCheckBoxControlChange( change ) {
								props.setAttributes( { hide_phone_icon: change } );
								props.setAttributes( { class_for_call_btn: ( ( change ? 'wp-call-button-block-button-no-phone' : 'wp-call-button-block-button' ) + ( btn_center_align ? ' wp-call-button-block-button-center' : ' wp-call-button-block-button-normal' ) ) } );
						}

						function onBtnCenterCheckBoxControlChange( change ) {
								props.setAttributes( { btn_center_align: change } );
								props.setAttributes( { class_for_call_btn: ( ( hide_phone_icon ? 'wp-call-button-block-button-no-phone' : 'wp-call-button-block-button' ) + ( change ? ' wp-call-button-block-button-center' : ' wp-call-button-block-button-normal' ) ) } );
						}
					
						function onFontSizeChange( newfontSize ) {
							props.setAttributes( { btn_font_size: newfontSize } );
						}

						return [
								el(
										InspectorControls,
										{ 
											key : 'controls' 
										},
										el(
											'div',
											{ className : 'wp-call-button-block-bottom-sep wp-call-button-block-top-sep wp-call-button-block-btn-color-picker' },
											el(
												'label',
												{},
												wpcallbtn_block_vars.call_btn_color + ':'
											),
											el(
												ColorPalette,
												{
													value: btn_color,
													onChange: onBtnColorChange,
													colors: btnColorsPalette,
												}
											)
										),
										el(
											'div',
											{ className : 'wp-call-button-block-bottom-sep wp-call-button-block-txt-color-picker' },
											el(
												'label',
												{},
												wpcallbtn_block_vars.call_btn_text_color + ':'
											),
											el(
												ColorPalette,
												{
													value: btn_txt_color,
													colors: txtColorsInPalette,
													onChange: onBtnTextColorChange
												}
											)
										),
										el(
												'div',
												{ className : 'wp-call-button-block-bottom-sep wp-call-button-block-btn-font-selector' },
												el(
														FontSizePicker,
														{
																key : 'fontSizePicker',
																fallbackFontSize: 16,
																onChange: onFontSizeChange,
																fontSizes: fontSizes,
																value: btn_font_size,
																disableCustomFontSizes: true
														}
												)
										),
										el(
												'div',
												{ className : 'wp-call-button-block-bottom-sep wp-call-button-block-phone-icon-checkbox' },
												el(
														'label',
														{},
														wpcallbtn_block_vars.call_btn_phone_icon_hide + ':'
												),
												el(
														CheckboxControl,
														{
																checked: hide_phone_icon,
																onChange: onCheckBoxControlChange
														}
												)
										),
										el(
												'div',
												{ className : 'wp-call-button-block-center-btn-checkbox' },
												el(
														'label',
														{},
														wpcallbtn_block_vars.call_btn_center_btn + ':'
												),
												el(
														CheckboxControl,
														{
																checked: btn_center_align,
																onChange: onBtnCenterCheckBoxControlChange
														}
												)
										)
									),
								el(
										RichText,
										{
												key: 'richtext',
												className: class_for_call_btn,
												onChange: onChangeContent,
												value: btn_text,
												placeholder: 'Add text…',
												keepPlaceholderOnFocus: true,
												formattingControls : [],
												multiline : false,
												style : { 
													color : btn_txt_color, 
													background: btn_color, 
													fontSize : btn_font_size ? btn_font_size + 'px' : undefined 
												}
										}
								)
						];
				},

				save: function( props ) {
						return null;
				}
    } );
}(
    window.wp.blocks,
    window.wp.editor,
    window.wp.element,
		window.wp.components
) );