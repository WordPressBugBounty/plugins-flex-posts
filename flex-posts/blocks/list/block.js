( function( blocks, components, editor, element, i18n ) {
	var el = element.createElement;
	var __ = i18n.__;

	var ServerSideRender;
	if ( typeof window.wp.serverSideRender !== 'undefined' ) {
		ServerSideRender = window.wp.serverSideRender;
	} else {
		ServerSideRender = components.ServerSideRender
	}

	var InspectorControls, InspectorAdvancedControls;
	if ( typeof window.wp.blockEditor !== 'undefined' ) {
		InspectorControls = window.wp.blockEditor.InspectorControls;
		InspectorAdvancedControls = window.wp.blockEditor.InspectorAdvancedControls;
	} else {
		InspectorControls = editor.InspectorControls;
		InspectorAdvancedControls = editor.InspectorAdvancedControls;
	}

	blocks.registerBlockType( 'flex-posts/list', {
		title: __( 'Flex Posts', 'flex-posts' ),

		icon: 'grid-view',

		category: 'widgets',

		supports: {
			align: [ 'wide', 'full' ],
			html: false,
		},

		attributes: flex_posts.attributes,

		edit: function( props ) {
			var attr = props.attributes;
			var layouts = [];
			var has_category_option = false;
			var has_post_tag_option = false;
			if ( typeof flex_posts.taxonomies[ attr.post_type ] !== 'undefined' ) {
				has_category_option = flex_posts.taxonomies[ attr.post_type ].indexOf( 'category' ) !== -1;
				has_post_tag_option = flex_posts.taxonomies[ attr.post_type ].indexOf( 'post_tag' ) !== -1;
			}
			if ( ! has_category_option ) {
				attr.cat = '';
			}
			if ( ! has_post_tag_option ) {
				attr.tag = '';
			}
			for ( i = 1; i <= flex_posts.layouts; i++ ) {
				layouts.push( { value: i, label: __( 'Layout', 'flex-posts' ) + ' ' + i } );
			}

			return [
				el( ServerSideRender, {
					block: 'flex-posts/list',
					attributes: props.attributes,
					key: 'server-render'
				} ),
				el(
					InspectorControls,
					{ key: 'inspector-general' },
					el(
						components.PanelBody,
						{
							title: __( 'General', 'flex-posts' ),
							initialOpen: true
						},
						el(
							components.TextControl,
							{
								type: 'text',
								label: __( 'Title', 'flex-posts' ),
								value: attr.title,
								onChange: function( val ) {
									props.setAttributes( { title: val } )
								},
								__nextHasNoMarginBottom: true,
								__next40pxDefaultSize: true
							}
						),
						el(
							components.TextControl,
							{
								type: 'text',
								label: __( 'Title URL', 'flex-posts' ),
								value: attr.title_url,
								onChange: function( val ) {
									props.setAttributes( { title_url: val } )
								},
								__nextHasNoMarginBottom: true,
								__next40pxDefaultSize: true
							}
						),
						el(
							components.SelectControl,
							{
								label: __( 'Layout', 'flex-posts' ),
								options: layouts,
								value: attr.layout,
								onChange: function( val ) {
									props.setAttributes( { layout: parseInt( val ) } )
								},
								__nextHasNoMarginBottom: true,
								__next40pxDefaultSize: true
							}
						),
					)
				),
				el(
					InspectorControls,
					{ key: 'inspector-query' },
					el(
						components.PanelBody,
						{
							title: __( 'Query', 'flex-posts' ),
							initialOpen: false
						},
						el(
							components.SelectControl,
							{
								label: __( 'Post Type', 'flex-posts' ),
								value: attr.post_type,
								options: flex_posts.post_types,
								onChange: function( val ) {
									props.setAttributes( { post_type: val } )
								},
								__nextHasNoMarginBottom: true,
								__next40pxDefaultSize: true
							}
						),
						has_category_option && el(
							components.SelectControl,
							{
								label: __( 'Category', 'flex-posts' ),
								value: attr.cat,
								options: flex_posts.categories,
								onChange: function( val ) {
									props.setAttributes( { cat: val } )
								},
								__nextHasNoMarginBottom: true,
								__next40pxDefaultSize: true
							}
						),
						has_post_tag_option && el(
							components.TextControl,
							{
								type: 'text',
								label: __( 'Tag(s)', 'flex-posts' ),
								value: attr.tag,
								onChange: function( val ) {
									props.setAttributes( { tag: val } )
								},
								__nextHasNoMarginBottom: true,
								__next40pxDefaultSize: true
							}
						),
						el(
							components.SelectControl,
							{
								label: __( 'Order by', 'flex-posts' ),
								value: attr.order_by,
								options: flex_posts.order_by,
								onChange: function( val ) {
									props.setAttributes( { order_by: val } )
								},
								__nextHasNoMarginBottom: true,
								__next40pxDefaultSize: true
							}
						),
						el(
							components.RangeControl,
							{
								label: __( 'Number of posts to show', 'flex-posts' ),
								value: attr.number,
								min: 1,
								onChange: function( val ) {
									props.setAttributes( { number: val } )
								},
								__nextHasNoMarginBottom: true,
								__next40pxDefaultSize: true
							}
						),
						el(
							components.RangeControl,
							{
								label: __( 'Number of posts to skip', 'flex-posts' ),
								value: attr.skip,
								min: 0,
								onChange: function( val ) {
									props.setAttributes( { skip: val } )
								},
								__nextHasNoMarginBottom: true,
								__next40pxDefaultSize: true
							}
						),
						el(
							components.CheckboxControl,
							{
								label: __( 'Exclude current post', 'flex-posts' ),
								checked: attr.exclude_current,
								onChange: function( val ) {
									props.setAttributes( { exclude_current: val } )
								},
								__nextHasNoMarginBottom: true
							}
						),
					)
				),
				el(
					InspectorControls,
					{ key: 'inspector-display' },
					el(
						components.PanelBody,
						{
							title: __( 'Display', 'flex-posts' ),
							initialOpen: false
						},
						el(
							components.SelectControl,
							{
								label: __( 'Show image on', 'flex-posts' ),
								value: attr.show_image,
								options: [
									{ value: 'all', label: __( 'All posts', 'flex-posts' ) },
									{ value: 'first', label: __( 'First post only', 'flex-posts' ) },
									{ value: 'none', label: __( 'None', 'flex-posts' ) }
								],
								onChange: function( val ) {
									props.setAttributes( { show_image: val } )
								},
								__nextHasNoMarginBottom: true,
								__next40pxDefaultSize: true
							}
						),
						attr.show_image !== 'none' && ( attr.layout === 1 || attr.layout === 3 ) && el(
							components.SelectControl,
							{
								label: __( 'Thumbnail image size', 'flex-posts' ),
								value: attr.image_size,
								options: flex_posts.image_sizes,
								onChange: function( val ) {
									props.setAttributes( { image_size: val } )
								},
								__nextHasNoMarginBottom: true,
								__next40pxDefaultSize: true
							}
						),
						attr.show_image !== 'none' && attr.layout !== 1 && el(
							components.SelectControl,
							{
								label: __( 'Medium image size', 'flex-posts' ),
								value: attr.image_size2,
								options: flex_posts.image_sizes,
								onChange: function( val ) {
									props.setAttributes( { image_size2: val } )
								},
								__nextHasNoMarginBottom: true,
								__next40pxDefaultSize: true
							}
						),
						el(
							components.CheckboxControl,
							{
								label: __( 'Show post title', 'flex-posts' ),
								checked: attr.show_title,
								onChange: function( val ) {
									props.setAttributes( { show_title: val } )
								},
								__nextHasNoMarginBottom: true
							}
						),
						el(
							components.CheckboxControl,
							{
								label: __( 'Show categories', 'flex-posts' ),
								checked: attr.show_categories,
								onChange: function( val ) {
									props.setAttributes( { show_categories: val } )
								},
								__nextHasNoMarginBottom: true
							}
						),
						el(
							components.CheckboxControl,
							{
								label: __( 'Show author', 'flex-posts' ),
								checked: attr.show_author,
								onChange: function( val ) {
									props.setAttributes( { show_author: val } )
								},
								__nextHasNoMarginBottom: true
							}
						),
						el(
							components.CheckboxControl,
							{
								label: __( 'Show author image', 'flex-posts' ),
								checked: attr.show_avatar,
								onChange: function( val ) {
									props.setAttributes( { show_avatar: val } )
								},
								__nextHasNoMarginBottom: true
							}
						),
						el(
							components.CheckboxControl,
							{
								label: __( 'Show date', 'flex-posts' ),
								checked: attr.show_date,
								onChange: function( val ) {
									props.setAttributes( { show_date: val } )
								},
								__nextHasNoMarginBottom: true
							}
						),
						el(
							components.CheckboxControl,
							{
								label: __( 'Show comments number', 'flex-posts' ),
								checked: attr.show_comments,
								onChange: function( val ) {
									props.setAttributes( { show_comments: val } )
								},
								__nextHasNoMarginBottom: true
							}
						),
						el(
							components.CheckboxControl,
							{
								label: __( 'Show excerpt', 'flex-posts' ),
								checked: attr.show_excerpt,
								onChange: function( val ) {
									props.setAttributes( { show_excerpt: val } )
								},
								__nextHasNoMarginBottom: true
							}
						),
						attr.show_excerpt && el(
							components.RangeControl,
							{
								label: __( 'Excerpt length', 'flex-posts' ),
								value: attr.excerpt_length,
								min: 1,
								onChange: function( val ) {
									props.setAttributes( { excerpt_length: val } )
								},
								__nextHasNoMarginBottom: true,
								__next40pxDefaultSize: true
							}
						),
						el(
							components.CheckboxControl,
							{
								label: __( 'Show read more link', 'flex-posts' ),
								checked: attr.show_readmore,
								onChange: function( val ) {
									props.setAttributes( { show_readmore: val } )
								},
								__nextHasNoMarginBottom: true
							}
						),
						attr.show_readmore && el(
							components.TextControl,
							{
								type: 'text',
								label: __( 'Read more text', 'flex-posts' ),
								value: attr.readmore_text,
								onChange: function( val ) {
									props.setAttributes( { readmore_text: val } )
								},
								__nextHasNoMarginBottom: true,
								__next40pxDefaultSize: true
							}
						),
						el(
							components.CheckboxControl,
							{
								label: __( 'Show pagination', 'flex-posts' ),
								checked: attr.pagination,
								onChange: function( val ) {
									props.setAttributes( { pagination: val } )
								},
								__nextHasNoMarginBottom: true
							}
						)
					)
				),
				el(
					InspectorAdvancedControls,
					{ key: 'inspector-advanced1' },
					el(
						'div',
						{},
						el(
							components.SelectControl,
							{
								label: __( 'Block Title HTML element', 'flex-posts' ),
								value: attr.block_title_el,
								options: flex_posts.title_el,
								onChange: function( val ) {
									props.setAttributes( { block_title_el: val } )
								},
								__nextHasNoMarginBottom: true,
								__next40pxDefaultSize: true
							}
						)
					)
				),
				el(
					InspectorAdvancedControls,
					{ key: 'inspector-advanced2' },
					el(
						'div',
						{},
						el(
							components.SelectControl,
							{
								label: __( 'Post Title HTML element', 'flex-posts' ),
								value: attr.post_title_el,
								options: flex_posts.title_el,
								onChange: function( val ) {
									props.setAttributes( { post_title_el: val } )
								},
								__nextHasNoMarginBottom: true,
								__next40pxDefaultSize: true
							},
						)
					)
				)
			];
		},

		save: function() {
			// Rendering in PHP
			return null;
		},
	} );
} )(
	window.wp.blocks,
	window.wp.components,
	window.wp.editor,
	window.wp.element,
	window.wp.i18n
);
