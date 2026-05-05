/**
 * WordPress dependencies
 */
import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { __ } from '@wordpress/i18n';
import { PanelRow, TextControl, TextareaControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';

const ExperienceMetaPanel = () => {
    const postType = useSelect( ( select ) => select( 'core/editor' ).getCurrentPostType(), [] );

    if ( postType !== 'experiencia' ) {
        return null;
    }

    // Obtenemos los metadatos usando useSelect
    const meta = useSelect( ( select ) => {
        return select( 'core/editor' ).getEditedPostAttribute( 'meta' );
    }, [] );

    // Obtenemos la función para actualizar usando useDispatch
    const { editPost } = useDispatch( 'core/editor' );

    if ( ! meta ) {
        return null;
    }

    const updateMeta = ( key, value ) => {
        editPost( { meta: { [ key ]: value } } );
    };


    return (
        <PluginDocumentSettingPanel
            name="ec-experience-meta"
            title={ __( 'Datos de la Experiencia', 'experience-crud' ) }
            className="ec-experience-meta-panel"
        >
            <PanelRow>
                <TextareaControl
                    label={ __( 'Resumen breve (Obligatorio)', 'experience-crud' ) }
                    help={ __( 'Aparecerá en el slider y se usará para SEO.', 'experience-crud' ) }
                    value={ meta.ec_summary || '' }
                    onChange={ ( val ) => updateMeta( 'ec_summary', val ) }
                />
            </PanelRow>

            <PanelRow>
                <TextControl
                    label={ __( 'Precio por persona (ARS)', 'experience-crud' ) }
                    type="number"
                    value={ meta.ec_price || 0 }
                    onChange={ ( val ) => updateMeta( 'ec_price', parseInt( val ) || 0 ) }
                />
            </PanelRow>

            <PanelRow>
                <TextControl
                    label={ __( 'Validez desde', 'experience-crud' ) }
                    type="date"
                    value={ meta.ec_price_valid_from || '' }
                    onChange={ ( val ) => updateMeta( 'ec_price_valid_from', val ) }
                />
            </PanelRow>

            <PanelRow>
                <TextControl
                    label={ __( 'Validez hasta', 'experience-crud' ) }
                    type="date"
                    value={ meta.ec_price_valid_to || '' }
                    onChange={ ( val ) => updateMeta( 'ec_price_valid_to', val ) }
                />
            </PanelRow>

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

            <PanelRow>
                <TextareaControl
                    label={ __( 'Productos relacionados', 'experience-crud' ) }
                    help={ __( 'Formato: Nombre | URL (uno por línea)', 'experience-crud' ) }
                    value={ meta.ec_related_products || '' }
                    onChange={ ( val ) => updateMeta( 'ec_related_products', val ) }
                />
            </PanelRow>

            <PanelRow>
                <TextareaControl
                    label={ __( 'Lista de precios (Legacy)', 'experience-crud' ) }
                    value={ meta.ec_prices_list || '' }
                    onChange={ ( val ) => updateMeta( 'ec_prices_list', val ) }
                />
            </PanelRow>
        </PluginDocumentSettingPanel>
    );
};

registerPlugin( 'ec-experience-meta-plugin', {
    render: ExperienceMetaPanel,
    icon: 'palmtree',
} );
