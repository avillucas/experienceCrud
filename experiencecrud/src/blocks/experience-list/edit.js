/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Internal dependencies
 */
import metadata from './block.json';

export default function Edit() {
    return (
        <div { ...useBlockProps() }>
            <ServerSideRender
                block={ metadata.name }
                attributes={ {} }
            />
        </div>
    );
}
