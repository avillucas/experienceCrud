import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import metadata from './block.json';

export default function Edit( { attributes, setAttributes } ) {
	const { introLogoUrl, introText, introBookingUrl, introEmail } = attributes;

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Panel introductorio', 'experience-crud' ) }>
					<TextControl
						label={ __( 'URL del logo / SVG', 'experience-crud' ) }
						value={ introLogoUrl }
						onChange={ ( val ) => setAttributes( { introLogoUrl: val } ) }
						help={ __( 'URL de la imagen o SVG que aparece en el panel de bienvenida.', 'experience-crud' ) }
					/>
					<TextareaControl
						label={ __( 'Texto descriptivo', 'experience-crud' ) }
						value={ introText }
						onChange={ ( val ) => setAttributes( { introText: val } ) }
						rows={ 5 }
						help={ __( 'Descripción general de las experiencias. Saltos de línea generan párrafos.', 'experience-crud' ) }
					/>
					<TextControl
						label={ __( 'URL de reservas', 'experience-crud' ) }
						value={ introBookingUrl }
						onChange={ ( val ) => setAttributes( { introBookingUrl: val } ) }
					/>
					<TextControl
						label={ __( 'Email de contacto', 'experience-crud' ) }
						value={ introEmail }
						onChange={ ( val ) => setAttributes( { introEmail: val } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...useBlockProps() }>
				<ServerSideRender
					block={ metadata.name }
					attributes={ attributes }
				/>
			</div>
		</>
	);
}
