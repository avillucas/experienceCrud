import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { __ } from '@wordpress/i18n';
import { PanelRow, TextControl, Button } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';

const Repeater = ( { label, items, onChange, fields } ) => {
    const updateItem = ( index, key, value ) => {
        const newItems = [ ...items ];
        newItems[ index ] = { ...newItems[ index ], [ key ]: value };
        onChange( newItems );
    };

    const addItem = () => {
        const newItem = fields.reduce( ( acc, f ) => ( { ...acc, [ f.key ]: '' } ), {} );
        onChange( [ ...items, newItem ] );
    };

    const removeItem = ( index ) => {
        onChange( items.filter( ( _, i ) => i !== index ) );
    };

    return (
        <div style={ { marginBottom: '20px', border: '1px solid #ddd', padding: '10px', borderRadius: '4px' } }>
            <label style={ { fontWeight: 'bold', display: 'block', marginBottom: '10px', fontSize: '11px', textTransform: 'uppercase' } }>{ label }</label>
            { items.map( ( item, index ) => (
                <div key={ index } style={ { marginBottom: '10px', display: 'flex', gap: '5px', alignItems: 'flex-start', background: '#f9f9f9', padding: '5px' } }>
                    <div style={ { flex: 1 } }>
                        { fields.map( ( f ) => (
                            <TextControl
                                key={ f.key }
                                label={ f.label }
                                value={ item[ f.key ] || '' }
                                onChange={ ( v ) => updateItem( index, f.key, v ) }
                                __nextHasNoMarginBottom
                            />
                        ) ) }
                    </div>
                    <Button isDestructive icon="trash" onClick={ () => removeItem( index ) } />
                </div>
            ) ) }
            <Button isSecondary onClick={ addItem }>{ __( 'Add Item', 'experience-crud' ) }</Button>
        </div>
    );
};

const ExperienceMetaPanel = () => {
    const postType = useSelect( ( select ) => select( 'core/editor' ).getCurrentPostType(), [] );
    if ( postType !== 'experiencia' ) return null;

    const meta = useSelect( ( select ) => select( 'core/editor' ).getEditedPostAttribute( 'meta' ), [] );
    const { editPost } = useDispatch( 'core/editor' );

    if ( ! meta ) return null;

    const updateMeta = ( key, value ) => editPost( { meta: { [ key ]: value } } );

    const getJsonMeta = ( key ) => {
        try {
            const value = meta[ key ];
            return typeof value === 'string' ? JSON.parse( value || '[]' ) : (value || []);
        } catch ( e ) {
            return [];
        }
    };

    const setJsonMeta = ( key, value ) => updateMeta( key, JSON.stringify( value ) );

    return (
        <PluginDocumentSettingPanel
            name="ec-experience-meta"
            title={ __( 'Experience Details', 'experience-crud' ) }
            className="ec-experience-meta-panel"
        >
            <PanelRow>
                <TextControl
                    label={ __( 'Duration (minutes)', 'experience-crud' ) }
                    type="number"
                    value={ meta.ec_duration }
                    onChange={ ( v ) => updateMeta( 'ec_duration', parseInt( v ) ) }
                    __nextHasNoMarginBottom
                />
            </PanelRow>
            
            <PanelRow>
                <TextControl
                    label={ __( 'Min Persons', 'experience-crud' ) }
                    type="number"
                    value={ meta.ec_min_persons }
                    onChange={ ( v ) => updateMeta( 'ec_min_persons', parseInt( v ) ) }
                    __nextHasNoMarginBottom
                />
            </PanelRow>

            <PanelRow>
                <TextControl
                    label={ __( 'Max Persons', 'experience-crud' ) }
                    type="number"
                    value={ meta.ec_max_persons }
                    onChange={ ( v ) => updateMeta( 'ec_max_persons', parseInt( v ) ) }
                    __nextHasNoMarginBottom
                />
            </PanelRow>

            <Repeater
                label={ __( 'Includes', 'experience-crud' ) }
                items={ getJsonMeta( 'ec_includes' ) }
                onChange={ ( v ) => setJsonMeta( 'ec_includes', v ) }
                fields={ [
                    { key: 'text', label: __( 'Text', 'experience-crud' ) },
                    { key: 'url', label: __( 'URL (Optional)', 'experience-crud' ) },
                ] }
            />

            <Repeater
                label={ __( 'Tastings', 'experience-crud' ) }
                items={ getJsonMeta( 'ec_tastings' ) }
                onChange={ ( v ) => setJsonMeta( 'ec_tastings', v ) }
                fields={ [
                    { key: 'text', label: __( 'Product Name', 'experience-crud' ) },
                    { key: 'url', label: __( 'Product URL (Optional)', 'experience-crud' ) },
                ] }
            />

            <Repeater
                label={ __( 'Requirements', 'experience-crud' ) }
                items={ getJsonMeta( 'ec_requirements' ) }
                onChange={ ( v ) => setJsonMeta( 'ec_requirements', v ) }
                fields={ [
                    { key: 'text', label: __( 'Requirement', 'experience-crud' ) },
                ] }
            />

            <PanelRow>
                <TextControl
                    label={ __( 'Contact Email', 'experience-crud' ) }
                    type="email"
                    value={ meta.ec_contact_email || '' }
                    onChange={ ( v ) => updateMeta( 'ec_contact_email', v ) }
                    __nextHasNoMarginBottom
                />
            </PanelRow>

            <PanelRow>
                <TextControl
                    label={ __( 'Booking URL', 'experience-crud' ) }
                    type="url"
                    value={ meta.ec_booking_url || '' }
                    onChange={ ( v ) => updateMeta( 'ec_booking_url', v ) }
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
