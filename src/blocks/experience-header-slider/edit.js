import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import './editor.css';

export default function Edit( { attributes, setAttributes } ) {
    return (
        <div { ...useBlockProps() }>
            <div className="ec-header-slider-edit">
                <RichText
                    tagName="h1"
                    value={ attributes.title }
                    onChange={ ( title ) => setAttributes( { title } ) }
                    placeholder={ __( 'Slider Title...', 'experience-crud' ) }
                />
                <RichText
                    tagName="p"
                    value={ attributes.description }
                    onChange={ ( description ) => setAttributes( { description } ) }
                    placeholder={ __( 'Slider Description...', 'experience-crud' ) }
                />
            </div>
        </div>
    );
}
