( function( blocks, element, i18n, editor, plugins, editPost, components, data, serverSideRender ) {
    var el = element.createElement;
    var __ = i18n.__;
    var registerBlockType = blocks.registerBlockType;
    var useBlockProps = editor.useBlockProps;
    var registerPlugin = plugins.registerPlugin;
    var PluginDocumentSettingPanel = editPost.PluginDocumentSettingPanel;
    var PanelRow = components.PanelRow;
    var TextControl = components.TextControl;
    var TextareaControl = components.TextareaControl;
    var useEntityProp = data.useEntityProp;

    // 1. Registro del Bloque Experience List
    registerBlockType( 'ec/experience-list', {
        apiVersion: 3,
        title: 'Experience List',
        icon: 'palmtree',
        category: 'widgets',
        edit: function() {
            return el( 'div', useBlockProps(), 
                el( serverSideRender, {
                    block: 'ec/experience-list',
                    attributes: {}
                } )
            );
        },
        save: function() { return null; } // Dynamic block
    } );

    // 2. Registro de la Sidebar para Metadatos
    var ExperienceMetaPanel = function() {
        var postType = data.useSelect( function( select ) {
            return select( 'core/editor' ).getCurrentPostType();
        }, [] );

        if ( postType !== 'experiencia' ) return null;

        var entityProp = useEntityProp( 'postType', postType, 'meta' );
        var meta = entityProp[0];
        var setMeta = entityProp[1];

        var updateMeta = function( key, value ) {
            var newMeta = {};
            newMeta[key] = value;
            setMeta( Object.assign( {}, meta, newMeta ) );
        };

        return el( PluginDocumentSettingPanel, {
            name: 'ec-experience-meta',
            title: __( 'Datos de la Experiencia', 'experience-crud' )
        }, 
            el( PanelRow, {}, el( TextControl, {
                label: __( 'Duración (minutos)', 'experience-crud' ),
                type: 'number',
                value: meta.ec_duration_min,
                onChange: function( val ) { updateMeta( 'ec_duration_min', parseInt( val ) ); }
            } ) ),
            el( PanelRow, {}, el( TextControl, {
                label: __( 'Mínimo de integrantes', 'experience-crud' ),
                type: 'number',
                value: meta.ec_min_members,
                onChange: function( val ) { updateMeta( 'ec_min_members', parseInt( val ) ); }
            } ) ),
            el( PanelRow, {}, el( TextControl, {
                label: __( 'Capacidad máxima', 'experience-crud' ),
                type: 'number',
                value: meta.ec_max_members,
                onChange: function( val ) { updateMeta( 'ec_max_members', parseInt( val ) ); }
            } ) ),
            el( PanelRow, {}, el( TextareaControl, {
                label: __( 'Lista de precios', 'experience-crud' ),
                value: meta.ec_prices_list,
                onChange: function( val ) { updateMeta( 'ec_prices_list', val ); }
            } ) ),
            el( PanelRow, {}, el( TextControl, {
                label: __( 'Email de contacto', 'experience-crud' ),
                type: 'email',
                value: meta.ec_contact_email,
                onChange: function( val ) { updateMeta( 'ec_contact_email', val ); }
            } ) ),
            el( PanelRow, {}, el( TextControl, {
                label: __( 'URL de reserva', 'experience-crud' ),
                type: 'url',
                value: meta.ec_booking_url,
                onChange: function( val ) { updateMeta( 'ec_booking_url', val ); }
            } ) )
        );
    };

    registerPlugin( 'ec-experience-meta-plugin', {
        render: ExperienceMetaPanel,
        icon: 'palmtree'
    } );

} )( 
    window.wp.blocks, 
    window.wp.element, 
    window.wp.i18n, 
    window.wp.blockEditor || window.wp.editor, 
    window.wp.plugins, 
    window.wp.editPost, 
    window.wp.components, 
    window.wp.data,
    window.wp.serverSideRender
);
