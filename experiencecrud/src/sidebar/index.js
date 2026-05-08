import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { __ } from '@wordpress/i18n';
import { PanelRow, TextControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';

const ExperienceMetaPanel = () => {
    const postType = useSelect( ( select ) => select( 'core/editor' ).getCurrentPostType(), [] );
    if ( postType !== 'experiencia' ) return null;

    const meta = useSelect( ( select ) => select( 'core/editor' ).getEditedPostAttribute( 'meta' ), [] );
    const { editPost } = useDispatch( 'core/editor' );

    if ( ! meta ) return null;

    const updateMeta = ( key, value ) => editPost( { meta: { [ key ]: value } } );

    return (
        <PluginDocumentSettingPanel
            name="ec-experience-meta"
            title={ __( 'Contact & Booking', 'experience-crud' ) }
            className="ec-experience-meta-panel"
        >
            <PanelRow>
                <TextControl
                    label={ __( 'Contact email', 'experience-crud' ) }
                    type="email"
                    value={ meta.ec_contact_email || '' }
                    onChange={ ( v ) => updateMeta( 'ec_contact_email', v ) }
                    placeholder="turismo@catenazapata.com"
                    __nextHasNoMarginBottom
                />
            </PanelRow>
            <PanelRow>
                <TextControl
                    label={ __( 'Booking URL', 'experience-crud' ) }
                    type="url"
                    value={ meta.ec_booking_url || '' }
                    onChange={ ( v ) => updateMeta( 'ec_booking_url', v ) }
                    placeholder="https://catenazapata.meitre.com/"
                    __nextHasNoMarginBottom
                />
            </PanelRow>
        </PluginDocumentSettingPanel>
    );
};

registerPlugin( 'ec-experience-meta-plugin', {
    render: ExperienceMetaPanel,
    icon: 'palmtree',
} );
