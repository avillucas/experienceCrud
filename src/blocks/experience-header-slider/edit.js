import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText, MediaUpload, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, Button } from '@wordpress/components';
import './editor.css';

export default function Edit( { attributes, setAttributes } ) {
    const { title, description, backgroundImage } = attributes;

    const onSelectImage = ( media ) => {
        setAttributes( { backgroundImage: media.url } );
    };

    return (
        <>
            <InspectorControls>
                <PanelBody title={ __( 'Slider Settings', 'experience-crud' ) }>
                    <MediaUpload
                        onSelect={ onSelectImage }
                        allowedTypes={ [ 'image' ] }
                        value={ backgroundImage }
                        render={ ( { open } ) => (
                            <Button isSecondary onClick={ open }>
                                { ! backgroundImage ? __( 'Select Background Image', 'experience-crud' ) : __( 'Change Background Image', 'experience-crud' ) }
                            </Button>
                        ) }
                    />
                </PanelBody>
            </InspectorControls>
            <div { ...useBlockProps() }>
                <div className="ec-header-slider-edit" style={ { backgroundImage: `url(${backgroundImage})`, backgroundSize: 'cover', backgroundPosition: 'center', minHeight: '300px', display: 'flex', flexDirection: 'column', justifyContent: 'center' } }>
                    <div className="ec-header-slider-edit-overlay" style={ { background: 'rgba(0,0,0,0.3)', padding: '40px' } }>
                        <RichText
                            tagName="h1"
                            value={ title }
                            onChange={ ( v ) => setAttributes( { title: v } ) }
                            placeholder={ __( 'Slider Title...', 'experience-crud' ) }
                            style={ { color: '#fff', textAlign: 'center' } }
                        />
                        <RichText
                            tagName="p"
                            value={ description }
                            onChange={ ( v ) => setAttributes( { description: v } ) }
                            placeholder={ __( 'Slider Description...', 'experience-crud' ) }
                            style={ { color: '#fff', textAlign: 'center' } }
                        />
                    </div>
                </div>
            </div>
        </>
    );
}
