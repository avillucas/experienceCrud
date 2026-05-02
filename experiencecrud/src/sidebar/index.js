/**
 * WordPress dependencies
 */
import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { __ } from '@wordpress/i18n';
import { PanelRow, TextControl, TextareaControl } from '@wordpress/components';
import { useEntityProp } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';

const ExperienceMetaPanel = () => {
    const postType = useSelect( ( select ) => select( 'core/editor' ).getCurrentPostType(), [] );

    if ( postType !== 'experiencia' ) {
        return null;
    }

    const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

    if ( ! meta ) {
        return null;
    }

    const updateMeta = ( key, value ) => {
        setMeta( { ...meta, [ key ]: value } );
    };


    return (
        <PluginDocumentSettingPanel
            name="ec-experience-meta"
            title={ __( 'Datos de la Experiencia', 'experience-crud' ) }
            className="ec-experience-meta-panel"
        >
            <PanelRow>
                <TextControl
                    label={ __( 'Duración (minutos)', 'experience-crud' ) }
                    type="number"
                    value={ meta.ec_duration_min || 0 }
                    onChange={ ( val ) => updateMeta( 'ec_duration_min', parseInt( val ) || 0 ) }
                />
            </PanelRow>
            <PanelRow>
                <TextControl
                    label={ __( 'Mínimo de integrantes', 'experience-crud' ) }
                    type="number"
                    value={ meta.ec_min_members || 0 }
                    onChange={ ( val ) => updateMeta( 'ec_min_members', parseInt( val ) || 0 ) }
                />
            </PanelRow>
            <PanelRow>
                <TextControl
                    label={ __( 'Capacidad máxima', 'experience-crud' ) }
                    type="number"
                    value={ meta.ec_max_members || 0 }
                    onChange={ ( val ) => updateMeta( 'ec_max_members', parseInt( val ) || 0 ) }
                />
            </PanelRow>
            <PanelRow>
                <TextareaControl
                    label={ __( 'Lista de precios', 'experience-crud' ) }
                    value={ meta.ec_prices_list || '' }
                    onChange={ ( val ) => updateMeta( 'ec_prices_list', val ) }
                />
            </PanelRow>
            <PanelRow>
                <TextControl
                    label={ __( 'Email de contacto', 'experience-crud' ) }
                    type="email"
                    value={ meta.ec_contact_email || '' }
                    onChange={ ( val ) => updateMeta( 'ec_contact_email', val ) }
                />
            </PanelRow>
            <PanelRow>
                <TextControl
                    label={ __( 'URL de reserva', 'experience-crud' ) }
                    type="url"
                    value={ meta.ec_booking_url || '' }
                    onChange={ ( val ) => updateMeta( 'ec_booking_url', val ) }
                />
            </PanelRow>
        </PluginDocumentSettingPanel>
    );
};

registerPlugin( 'ec-experience-meta-plugin', {
    render: ExperienceMetaPanel,
    icon: 'palmtree',
} );
