/**
 * Flex Posts - Block Helpers
 *
 * This file exports reusable helper functions for creating block controls.
 */
( function( wp ) {
	'use strict';

	const { __ } = wp.i18n;

	/**
	 * Create a text control element
	 *
	 * @param {wp.element.createElement} el    - createElement function
	 * @param {string}                       label     - Pre-translated label text
	 * @param {string}                        attrKey   - Attribute key to update
	 * @param {Object}                        attr      - Current attributes object
	 * @param {Function}                      setAttributes - Function to update attributes
	 * @return {JSX.Element} TextControl element
	 */
	const createTextControl = ( el, label, attrKey, attr, setAttributes ) =>
		el( wp.components.TextControl, {
			type: 'text',
			label: label,
			value: attr[ attrKey ],
			onChange: ( val ) => setAttributes( { [ attrKey ]: val } ),
			__nextHasNoMarginBottom: true,
			__next40pxDefaultSize: true
		} );

	/**
	 * Create a select control element
	 *
	 * @param {wp.element.createElement} el    - createElement function
	 * @param {string}                       label     - Pre-translated label text
	 * @param {string}                        attrKey   - Attribute key to update
	 * @param {Object}                        attr      - Current attributes object
	 * @param {Function}                      setAttributes - Function to update attributes
	 * @param {Array}                         options   - Select options array
	 * @param {Function|null}                 parser    - Optional parser function for the value
	 * @return {JSX.Element} SelectControl element
	 */
	const createSelectControl = ( el, label, attrKey, attr, setAttributes, options, parser = null ) =>
		el( wp.components.SelectControl, {
			label: label,
			value: attr[ attrKey ],
			options: options,
			onChange: ( val ) => setAttributes( { [ attrKey ]: parser ? parser( val ) : val } ),
			__nextHasNoMarginBottom: true,
			__next40pxDefaultSize: true
		} );

	/**
	 * Create a range control element
	 *
	 * @param {wp.element.createElement} el    - createElement function
	 * @param {string}                       label     - Pre-translated label text
	 * @param {string}                        attrKey   - Attribute key to update
	 * @param {Object}                        attr      - Current attributes object
	 * @param {Function}                      setAttributes - Function to update attributes
	 * @param {number}                        min       - Minimum value (default: 0)
	 * @param {number}                        max       - Maximum value (default: 100)
	 * @return {JSX.Element} RangeControl element
	 */
	const createRangeControl = ( el, label, attrKey, attr, setAttributes, min = 0, max = 100 ) =>
		el( wp.components.RangeControl, {
			label: label,
			value: attr[ attrKey ],
			min: min,
			max: max,
			onChange: ( val ) => setAttributes( { [ attrKey ]: val } ),
			__nextHasNoMarginBottom: true,
			__next40pxDefaultSize: true
		} );

	/**
	 * Create a checkbox control element
	 *
	 * @param {wp.element.createElement} el    - createElement function
	 * @param {string}                       label     - Pre-translated label text
	 * @param {string}                        attrKey   - Attribute key to update
	 * @param {Object}                        attr      - Current attributes object
	 * @param {Function}                      setAttributes - Function to update attributes
	 * @return {JSX.Element} CheckboxControl element
	 */
	const createCheckboxControl = ( el, label, attrKey, attr, setAttributes ) =>
		el( wp.components.CheckboxControl, {
			label: label,
			checked: attr[ attrKey ],
			onChange: ( val ) => setAttributes( { [ attrKey ]: val } ),
			__nextHasNoMarginBottom: true
		} );

	/**
	 * Create a panel body with controls
	 *
	 * @param {wp.element.createElement} el         - createElement function
	 * @param {string}                       title      - Pre-translated panel title
	 * @param {boolean}                      initialOpen - Whether panel starts open
	 * @param {Array}                        controls   - Array of control elements
	 * @return {JSX.Element} PanelBody element
	 */
	const createPanelBody = ( el, title, initialOpen, controls ) =>
		el( wp.components.PanelBody, { title: title, initialOpen: initialOpen }, ...controls );

	/**
	 * Create a visual layout selector component
	 *
	 * @param {wp.element.createElement} el         - createElement function
	 * @param {string}                       label      - Pre-translated label text
	 * @param {string}                        attrKey   - Attribute key to update
	 * @param {Object}                        attr      - Current attributes object
	 * @param {Function}                      setAttributes - Function to update attributes
	 * @param {Object}                        layoutSVGs - Object mapping layout numbers to SVG strings
	 * @param {string}                        more - more text
	 * @return {JSX.Element} Layout selector component
	 */
	const createLayoutSelector = ( el, label, attrKey, attr, setAttributes, layoutSVGs, more = '' ) => {
		const layoutItems = [];
		const layoutCount = Object.keys( layoutSVGs ).length;

		for ( let i = 1; i <= layoutCount; i++ ) {
			const isSelected = parseInt( attr[ attrKey ] ) === i;
			const svgString = layoutSVGs[ i ];

			layoutItems.push(
				el( 'button', {
					key: `layout-${ i }`,
					type: 'button',
					className: `flex-posts-layout-btn${ isSelected ? ' is-selected' : '' }`,
					onClick: () => setAttributes( { [ attrKey ]: i } ),
					'aria-label': __( 'Layout', 'flex-posts' ) + ' ' + i,
					'aria-pressed': isSelected
				},
					el( 'span', { className: 'flex-posts-layout-preview', dangerouslySetInnerHTML: { __html: svgString } } )
				)
			);
		}

		return el( 'div', { className: 'flex-posts-layout-selector' },
			el( 'label', { className: 'flex-posts-layout-selector__label' }, label ),
			el( 'div', { className: 'flex-posts-layout-selector__grid' }, ...layoutItems ),
			flex_posts.more_url && el( 'a', {
				className: 'flex-posts-layout-selector__more-link',
				href: flex_posts.more_url,
				target: '_blank',
				rel: 'noopener noreferrer'
			}, more )
		);
	};

	/**
	 * Decode HTML entities in a string
	 *
	 * @param {string} str String with HTML entities
	 * @return {string} Decoded string
	 */
	const decodeHtmlEntities = ( str ) => {
		const textarea = document.createElement( 'textarea' );
		textarea.innerHTML = str;
		return textarea.value;
	};

	/**
	 * Hook to fetch and format categories from WordPress.
	 *
	 * @return {Array} Formatted categories array with label/value pairs
	 */
	const useCategories = () => {
		const { useSelect } = wp.data;
		const { useMemo } = wp.element;

		// Fetch raw categories
		const rawCategories = useSelect( ( select ) => {
			return select( 'core' ).getEntityRecords( 'taxonomy', 'category', { per_page: -1 } );
		}, [] );

		// Format categories with memoization
		const categories = useMemo( () => {
			const formattedCategories = [
				{
					label: __( 'All Categories', 'flex-posts' ),
					value: '',
				},
			];
			if ( rawCategories && rawCategories.length > 0 ) {
				rawCategories.forEach( ( cat ) => {
					formattedCategories.push( {
						label: decodeHtmlEntities( cat.name ),
						value: cat.id,
					} );
				} );
			}
			return formattedCategories;
		}, [ rawCategories ] );

		return categories;
	};

	/**
	 * Hook to fetch post types and their taxonomies directly from the editor data store.
	 *
	 *   - options:     [{ label, value }] with Post and Page first, public custom
	 *                  post types in the middle, and an "Any" entry last.
	 *   - taxonomies:  { [postTypeSlug]: string[] } map used to decide whether the
	 *                  Category / Tag controls apply to the selected post type.
	 *   - hasResolved: false until the REST request has returned, so callers can
	 *                  avoid acting on an empty taxonomy map while it loads.
	 *
	 * @return {{ options: Array, taxonomies: Object, hasResolved: boolean }}
	 */
	const usePostTypes = () => {
		const { useSelect } = wp.data;
		const { useMemo } = wp.element;

		// Edit context is required to read `viewable` and `labels.singular_name`.
		const rawPostTypes = useSelect( ( select ) => {
			return select( 'core' ).getPostTypes( { per_page: -1, context: 'edit' } );
		}, [] );

		return useMemo( () => {
			// Post and Page are always offered first, matching the previous PHP output.
			const options = [
				{ label: __( 'Post', 'flex-posts' ), value: 'post' },
				{ label: __( 'Page', 'flex-posts' ), value: 'page' },
			];
			const taxonomies = {};

			// Post types that exist in WordPress but should never be selectable here.
			const reserved = [ 'post', 'page', 'attachment' ];

			if ( rawPostTypes && rawPostTypes.length > 0 ) {
				rawPostTypes.forEach( ( type ) => {
					if ( Array.isArray( type.taxonomies ) ) {
						taxonomies[ type.slug ] = type.taxonomies;
					}

					// Skip built-ins and non-public types to mirror
					// get_post_types( array( 'public' => true, '_builtin' => false ) ).
					if ( reserved.indexOf( type.slug ) !== -1 || ! type.viewable ) {
						return;
					}

					const label = ( type.labels && type.labels.singular_name )
						? type.labels.singular_name
						: type.name;
					options.push( { label: label, value: type.slug } );
				} );
			}

			options.push( { label: __( 'Any', 'flex-posts' ), value: 'any' } );

			return {
				options: options,
				taxonomies: taxonomies,
				hasResolved: Array.isArray( rawPostTypes ),
			};
		}, [ rawPostTypes ] );
	};

	/**
	 * Clear invalid taxonomy values based on available options
	 *
	 * @param {boolean} hasCategoryOption - Whether categories are supported for the current post type
	 * @param {boolean} hasPostTagOption  - Whether post tags are supported for the current post type
	 * @param {Object}  attr              - Current block attributes
	 * @return {Object} Updates object with cleared invalid values
	 */
	const clearInvalidTaxonomies = ( hasCategoryOption, hasPostTagOption, attr ) => {
		const updates = {};
		if ( ! hasCategoryOption && attr.cat ) {
			updates.cat = '';
		}
		if ( ! hasPostTagOption && attr.tag ) {
			updates.tag = '';
		}
		return updates;
	};

	/**
	 * Shared label for a known checkbox key. Returns undefined for unknown keys
	 * so callers can fall back to an explicit label.
	 */
	const sharedCheckboxLabel = ( key ) => ( {
		show_title:      __( 'Show post title', 'flex-posts' ),
		show_categories: __( 'Show categories', 'flex-posts' ),
		show_author:     __( 'Show author', 'flex-posts' ),
		show_avatar:     __( 'Show author image', 'flex-posts' ),
		show_date:       __( 'Show date', 'flex-posts' ),
		show_comments:   __( 'Show comments number', 'flex-posts' )
	}[ key ] );

	/**
	 * Render the Layout panel.
	 */
	const renderLayoutPanel = ( el, attr, setAttributes, layoutSVGs ) =>
		createPanelBody( el, __( 'Layout', 'flex-posts' ), true, [
			createLayoutSelector( el, __( 'Choose a layout', 'flex-posts' ), 'layout', attr, setAttributes, layoutSVGs, __( 'More layouts', 'flex-posts' ) )
		] );

	/**
	 * Render the Heading panel.
	 */
	const renderHeadingPanel = ( el, attr, setAttributes ) => {
		const controls = [
			createCheckboxControl( el, __( 'Use category title', 'flex-posts' ), 'title_cat', attr, setAttributes )
		];

		if ( ! attr.title_cat ) {
			controls.push( createTextControl( el, __( 'Title', 'flex-posts' ), 'title', attr, setAttributes ) );
		}

		controls.push( createCheckboxControl( el, __( 'Use category URL', 'flex-posts' ), 'title_url_cat', attr, setAttributes ) );

		if ( ! attr.title_url_cat ) {
			controls.push( createTextControl( el, __( 'Title URL', 'flex-posts' ), 'title_url', attr, setAttributes ) );
		}

		return createPanelBody( el, __( 'Heading', 'flex-posts' ), false, controls );
	};

	/**
	 * Render the Query panel.
	 *
	 * @param {Object}   ctx     { hasCategoryOption, hasPostTagOption, categories, postTypeOptions }
	 * @param {Object}   options { numberOfPosts: bool }
	 * @param {Function} [options.extraControls] ( el, attr, setAttributes ) => control[] appended at the end
	 */
	const renderQueryPanel = ( el, attr, setAttributes, ctx, options ) => {
		const controls = [
			createSelectControl( el, __( 'Post Type', 'flex-posts' ), 'post_type', attr, setAttributes, ctx.postTypeOptions )
		];

		if ( ctx.hasCategoryOption ) {
			controls.push( createSelectControl( el, __( 'Category', 'flex-posts' ), 'cat', attr, setAttributes, ctx.categories ) );
		}

		if ( ctx.hasPostTagOption ) {
			controls.push( createTextControl( el, __( 'Tag(s)', 'flex-posts' ), 'tag', attr, setAttributes ) );
		}

		controls.push( createSelectControl( el, __( 'Order by', 'flex-posts' ), 'order_by', attr, setAttributes, flex_posts.order_by ) );

		if ( options.numberOfPosts ) {
			controls.push( createRangeControl( el, __( 'Number of posts to show', 'flex-posts' ), 'number', attr, setAttributes, 1 ) );
		}

		controls.push(
			createRangeControl( el, __( 'Number of posts to skip', 'flex-posts' ), 'skip', attr, setAttributes, 0 ),
			createCheckboxControl( el, __( 'Exclude current post', 'flex-posts' ), 'exclude_current', attr, setAttributes )
		);

		if ( options.extraControls ) {
			controls.push( ...options.extraControls( el, attr, setAttributes ) );
		}

		return createPanelBody( el, __( 'Query', 'flex-posts' ), false, controls );
	};

	/**
	 * Render the Display panel.
	 *
	 * @param {Object} options
	 *   showImage     bool  — adds show_image select + conditional image_size selects
	 *   numericRanges [{ key, label, min, max }] — range controls before the checkboxes
	 *   checkboxes    [{ key, label? }] — label is optional; falls back to sharedCheckboxLabel(key)
	 *   excerpt       bool  — adds show_excerpt checkbox + conditional excerpt_length range
	 *   readmore      bool  — adds show_readmore checkbox + conditional readmore_text input
	 *   pagination    bool  — adds the pagination checkbox at the end
	 *   extraControls Function — ( el, attr, setAttributes ) => control[] appended at the very end
	 */
	const renderDisplayPanel = ( el, attr, setAttributes, options ) => {
		const controls = [];

		if ( options.showImage ) {
			const showImageOptions = [
				{ value: 'all',   label: __( 'All posts', 'flex-posts' ) },
				{ value: 'first', label: __( 'First post only', 'flex-posts' ) },
				{ value: 'none',  label: __( 'None', 'flex-posts' ) }
			];
			controls.push( createSelectControl( el, __( 'Show image on', 'flex-posts' ), 'show_image', attr, setAttributes, showImageOptions ) );

			if ( attr.show_image !== 'none' && attr.layout !== 1 ) {
				controls.push( createSelectControl( el, __( 'Image size', 'flex-posts' ), 'image_size2', attr, setAttributes, flex_posts.image_sizes ) );
			}

			if ( attr.show_image !== 'none' && ( attr.layout === 1 || attr.layout === 3 ) ) {
				controls.push( createSelectControl( el, __( 'Thumbnail image size', 'flex-posts' ), 'image_size', attr, setAttributes, flex_posts.image_sizes ) );
			}
		}

		( options.numericRanges || [] ).forEach( ( { key, label, min = 0, max = 100 } ) => {
			controls.push( createRangeControl( el, label, key, attr, setAttributes, min, max ) );
		} );

		( options.checkboxes || [] ).forEach( ( { key, label } ) => {
			controls.push( createCheckboxControl( el, label || sharedCheckboxLabel( key ) || key, key, attr, setAttributes ) );
		} );

		if ( options.excerpt ) {
			controls.push( createCheckboxControl( el, __( 'Show excerpt', 'flex-posts' ), 'show_excerpt', attr, setAttributes ) );
			if ( attr.show_excerpt ) {
				controls.push( createRangeControl( el, __( 'Excerpt length', 'flex-posts' ), 'excerpt_length', attr, setAttributes, 1 ) );
			}
		}

		if ( options.readmore ) {
			controls.push( createCheckboxControl( el, __( 'Show read more link', 'flex-posts' ), 'show_readmore', attr, setAttributes ) );
			if ( attr.show_readmore ) {
				controls.push( createTextControl( el, __( 'Read more text', 'flex-posts' ), 'readmore_text', attr, setAttributes ) );
			}
		}

		if ( options.pagination ) {
			controls.push( createCheckboxControl( el, __( 'Show pagination', 'flex-posts' ), 'pagination', attr, setAttributes ) );
		}

		if ( options.extraControls ) {
			controls.push( ...options.extraControls( el, attr, setAttributes ) );
		}

		return createPanelBody( el, __( 'Display', 'flex-posts' ), false, controls );
	};

	/**
	 * Render Inspector Advanced Controls (Block / Post title HTML element selectors).
	 */
	const renderAdvancedPanels = ( el, attr, setAttributes ) => {
		const { InspectorAdvancedControls } = wp.blockEditor;
		return [
			el( InspectorAdvancedControls, { key: 'inspector-advanced1' },
				createSelectControl( el, __( 'Block Title HTML element', 'flex-posts' ), 'block_title_el', attr, setAttributes, flex_posts.title_el )
			),
			el( InspectorAdvancedControls, { key: 'inspector-advanced2' },
				createSelectControl( el, __( 'Post Title HTML element', 'flex-posts' ), 'post_title_el', attr, setAttributes, flex_posts.title_el )
			)
		];
	};

	/**
	 * Resolve the ServerSideRender component across WP versions.
	 */
	const resolveServerSideRender = () => {
		if ( typeof wp.serverSideRender !== 'undefined' ) {
			return ( typeof wp.serverSideRender.ServerSideRender !== 'undefined' )
				? wp.serverSideRender.ServerSideRender
				: wp.serverSideRender;
		}
		return wp.components.ServerSideRender;
	};

	/**
	 * Register a Flex Posts-style block.
	 *
	 * Owns the boilerplate, edit() function, and registerBlockType call.
	 * Each block.js declares only what differs.
	 *
	 * @param {Object} config
	 *   blockName   string   — e.g. 'flex-posts/list'
	 *   title       string   — pre-translated title
	 *   icon        string   — dashicon name (default 'grid-view')
	 *   attributes  Object   — block attributes definition
	 *   layoutSVGs  Object   — map of layout number → SVG string
	 *   supports    Object   — extra block supports merged over the defaults (e.g. color, typography)
	 *   query       Object   — { numberOfPosts: bool }
	 *   display     Object   — see renderDisplayPanel options
	 *   extraPanels Function — ( el, attr, setAttributes ) => panel[] appended after the Display panel
	 */
	const registerFlexBlock = ( config ) => {
		const {
			blockName,
			title,
			icon = 'grid-view',
			category,
			attributes,
			layoutSVGs,
			supports = {},
			query: queryOptions = {},
			display: displayOptions = {},
			extraPanels
		} = config;

		const blockCategory = category
			|| ( ( typeof flex_posts !== 'undefined' && flex_posts.category ) ? flex_posts.category : 'widgets' );

		const ServerSideRender = resolveServerSideRender();
		const { createElement: el, Fragment, useEffect } = wp.element;
		const { useBlockProps, InspectorControls } = wp.blockEditor;
		const { Disabled } = wp.components;

		wp.blocks.registerBlockType( blockName, {
			apiVersion: 3,
			title: title,
			icon: icon,
			category: blockCategory,
			supports: {
				align: [ 'wide', 'full' ],
				html: false,
				...supports
			},
			attributes: attributes,

			edit: function( props ) {
				const { attributes: attr, setAttributes } = props;

				const categories = useCategories();
				const { options: postTypeOptions, taxonomies, hasResolved: postTypesResolved } = usePostTypes();

				let hasCategoryOption = false;
				let hasPostTagOption = false;
				const currentTaxonomies = taxonomies[ attr.post_type ];
				if ( typeof currentTaxonomies !== 'undefined' ) {
					hasCategoryOption = currentTaxonomies.indexOf( 'category' ) !== -1;
					hasPostTagOption = currentTaxonomies.indexOf( 'post_tag' ) !== -1;
				}

				useEffect( () => {
					if ( ! postTypesResolved ) {
						return;
					}
					const updates = clearInvalidTaxonomies( hasCategoryOption, hasPostTagOption, attr );
					if ( Object.keys( updates ).length > 0 ) {
						setAttributes( updates );
					}
				}, [ attr.post_type, hasCategoryOption, hasPostTagOption, postTypesResolved ] );

				return el( Fragment, null,
					el( 'div', useBlockProps(),
						el( Disabled, null,
							el( ServerSideRender, {
								skipBlockSupportAttributes: true,
								block: blockName,
								attributes: attr,
								key: 'server-render'
							} )
						)
					),
					el( InspectorControls, { key: 'inspector' },
						renderLayoutPanel( el, attr, setAttributes, layoutSVGs ),
						renderHeadingPanel( el, attr, setAttributes ),
						renderQueryPanel( el, attr, setAttributes,
							{ hasCategoryOption, hasPostTagOption, categories, postTypeOptions },
							queryOptions
						),
						renderDisplayPanel( el, attr, setAttributes, displayOptions ),
						...( extraPanels ? extraPanels( el, attr, setAttributes ) : [] )
					),
					...renderAdvancedPanels( el, attr, setAttributes )
				);
			},

			save: function() {
				return null;
			}
		} );
	};

	// Export helpers to global namespace
	window.flexPostsHelpers = {
		createTextControl,
		createSelectControl,
		createRangeControl,
		createCheckboxControl,
		createPanelBody,
		createLayoutSelector,
		decodeHtmlEntities,
		useCategories,
		usePostTypes,
		clearInvalidTaxonomies,
		registerFlexBlock
	};

} )( window.wp );