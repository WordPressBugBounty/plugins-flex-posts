( function( wp ) {
	const { __ } = wp.i18n;
	const { registerFlexBlock } = window.flexPostsHelpers;

	/**
	 * Default layout SVGs defined in JavaScript.
	 * These can be extended or overridden via the PHP filter flex_posts_layout_svgs.
	 */
	const defaultLayoutSVGs = {
		1: '<svg xmlns="http://www.w3.org/2000/svg" width="60" height="48" viewBox="0 0 100 80"><rect x="10" y="10" width="12" height="12" fill="#eee" stroke="#bbb" stroke-width="1"/><rect x="10" y="30" width="12" height="12" fill="#eee" stroke="#bbb" stroke-width="1"/><rect x="10" y="50" width="12" height="12" fill="#eee" stroke="#bbb" stroke-width="1"/><rect x="55" y="10" width="12" height="12" fill="#eee" stroke="#bbb" stroke-width="1"/><rect x="55" y="30" width="12" height="12" fill="#eee" stroke="#bbb" stroke-width="1"/><rect x="55" y="50" width="12" height="12" fill="#eee" stroke="#bbb" stroke-width="1"/><rect x="26" y="10" width="18" height="4" fill="#ddd"/><rect x="26" y="30" width="18" height="4" fill="#ddd"/><rect x="26" y="50" width="18" height="4" fill="#ddd"/><rect x="71" y="10" width="18" height="4" fill="#ddd"/><rect x="71" y="30" width="18" height="4" fill="#ddd"/><rect x="71" y="50" width="18" height="4" fill="#ddd"/><rect x="26" y="15" width="10" height="4" fill="#eee"/><rect x="26" y="35" width="10" height="4" fill="#eee"/><rect x="26" y="55" width="10" height="4" fill="#eee"/><rect x="71" y="15" width="10" height="4" fill="#eee"/><rect x="71" y="35" width="10" height="4" fill="#eee"/><rect x="71" y="55" width="10" height="4" fill="#eee"/></svg>',
		2: '<svg xmlns="http://www.w3.org/2000/svg" width="60" height="48" viewBox="0 0 100 80"><rect x="10" y="10" width="35" height="20" fill="#eee" stroke="#bbb" stroke-width="1"/><rect x="55" y="10" width="35" height="20" fill="#eee" stroke="#bbb" stroke-width="1"/><rect x="10" y="44" width="35" height="20" fill="#eee" stroke="#bbb" stroke-width="1"/><rect x="55" y="44" width="35" height="20" fill="#eee" stroke="#bbb" stroke-width="1"/><rect x="10" y="34" width="25" height="4" fill="#ddd"/><rect x="55" y="34" width="25" height="4" fill="#ddd"/><rect x="10" y="68" width="25" height="4" fill="#ddd"/><rect x="55" y="68" width="25" height="4" fill="#ddd"/></svg>',
		3: '<svg xmlns="http://www.w3.org/2000/svg" width="60" height="48" viewBox="0 0 100 80"><rect x="10" y="10" width="35" height="25" fill="#eee" stroke="#bbb" stroke-width="1"/><rect x="10" y="40" width="35" height="4" fill="#ddd"/><rect x="10" y="47" width="20" height="4" fill="#eee"/><rect x="10" y="55" width="35" height="4" fill="#eee"/><rect x="10" y="60" width="35" height="4" fill="#eee"/><rect x="55" y="10" width="12" height="12" fill="#eee" stroke="#bbb" stroke-width="1"/><rect x="55" y="30" width="12" height="12" fill="#eee" stroke="#bbb" stroke-width="1"/><rect x="55" y="50" width="12" height="12" fill="#eee" stroke="#bbb" stroke-width="1"/><rect x="71" y="10" width="18" height="4" fill="#ddd"/><rect x="71" y="30" width="18" height="4" fill="#ddd"/><rect x="71" y="50" width="18" height="4" fill="#ddd"/><rect x="71" y="15" width="10" height="4" fill="#eee"/><rect x="71" y="35" width="10" height="4" fill="#eee"/><rect x="71" y="55" width="10" height="4" fill="#eee"/></svg>',
		4: '<svg xmlns="http://www.w3.org/2000/svg" width="60" height="48" viewBox="0 0 100 80"><rect x="10" y="10" width="27" height="20" fill="#eee" stroke="#bbb" stroke-width="1"/><rect x="10" y="40" width="27" height="20" fill="#eee" stroke="#bbb" stroke-width="1"/><rect x="45" y="10" width="45" height="5" fill="#ddd"/><rect x="45" y="18" width="35" height="5" fill="#eee"/><rect x="45" y="40" width="45" height="5" fill="#ddd"/><rect x="45" y="48" width="35" height="5" fill="#eee"/></svg>',
		5: '<svg xmlns="http://www.w3.org/2000/svg" width="60" height="48" viewBox="0 0 100 80"><rect x="10" y="10" width="22" height="15" fill="#eee" stroke="#bbb" stroke-width="1" /><rect x="40" y="10" width="22" height="15" fill="#eee" stroke="#bbb" stroke-width="1" /><rect x="70" y="10" width="22" height="15" fill="#eee" stroke="#bbb" stroke-width="1" /><rect x="10" y="40" width="22" height="15" fill="#eee" stroke="#bbb" stroke-width="1" /><rect x="40" y="40" width="22" height="15" fill="#eee" stroke="#bbb" stroke-width="1" /><rect x="70" y="40" width="22" height="15" fill="#eee" stroke="#bbb" stroke-width="1" /><rect x="10" y="29" width="20" height="4" fill="#ddd" /><rect x="40" y="29" width="20" height="4" fill="#ddd" /><rect x="70" y="29" width="20" height="4" fill="#ddd" /><rect x="10" y="59" width="20" height="4" fill="#ddd" /><rect x="40" y="59" width="20" height="4" fill="#ddd" /><rect x="70" y="59" width="20" height="4" fill="#ddd" /></svg>',
		6: '<svg xmlns="http://www.w3.org/2000/svg" width="60" height="48" viewBox="0 0 100 80"><rect x="10" y="10" width="15" height="10" fill="#eee" stroke="#bbb" stroke-width="1" /><rect x="32" y="10" width="15" height="10" fill="#eee" stroke="#bbb" stroke-width="1" /><rect x="54" y="10" width="15" height="10" fill="#eee" stroke="#bbb" stroke-width="1" /><rect x="76" y="10" width="15" height="10" fill="#eee" stroke="#bbb" stroke-width="1" /><rect x="10" y="33" width="15" height="10" fill="#eee" stroke="#bbb" stroke-width="1" /><rect x="32" y="33" width="15" height="10" fill="#eee" stroke="#bbb" stroke-width="1" /><rect x="54" y="33" width="15" height="10" fill="#eee" stroke="#bbb" stroke-width="1" /><rect x="76" y="33" width="15" height="10" fill="#eee" stroke="#bbb" stroke-width="1" /><rect x="10" y="56" width="15" height="10" fill="#eee" stroke="#bbb" stroke-width="1" /><rect x="32" y="56" width="15" height="10" fill="#eee" stroke="#bbb" stroke-width="1" /><rect x="54" y="56" width="15" height="10" fill="#eee" stroke="#bbb" stroke-width="1" /><rect x="76" y="56" width="15" height="10" fill="#eee" stroke="#bbb" stroke-width="1" /><rect x="10" y="23" width="13" height="4" fill="#ddd" /><rect x="32" y="23" width="13" height="4" fill="#ddd" /><rect x="54" y="23" width="13" height="4" fill="#ddd" /><rect x="76" y="23" width="13" height="4" fill="#ddd" /><rect x="10" y="46" width="13" height="4" fill="#ddd" /><rect x="32" y="46" width="13" height="4" fill="#ddd" /><rect x="54" y="46" width="13" height="4" fill="#ddd" /><rect x="76" y="46" width="13" height="4" fill="#ddd" /><rect x="10" y="69" width="13" height="4" fill="#ddd" /><rect x="32" y="69" width="13" height="4" fill="#ddd" /><rect x="54" y="69" width="13" height="4" fill="#ddd" /><rect x="76" y="69" width="13" height="4" fill="#ddd" /></svg>'
	};

	// Merge default SVGs with SVGs passed from PHP. PHP-passed SVGs take precedence.
	const phpLayoutSVGs = flex_posts.layout_svgs || {};
	const layoutSVGs = { ...defaultLayoutSVGs, ...phpLayoutSVGs };

	registerFlexBlock( {
		blockName:  'flex-posts/list',
		title:      __( 'Flex Posts', 'flex-posts' ),
		attributes: flex_posts.attributes,
		layoutSVGs: layoutSVGs,
		query: {
			numberOfPosts: true
		},
		display: {
			showImage: true,
			checkboxes: [
				{ key: 'show_title' },
				{ key: 'show_categories' },
				{ key: 'show_author' },
				{ key: 'show_avatar' },
				{ key: 'show_date' },
				{ key: 'show_comments' }
			],
			excerpt:    true,
			readmore:   true,
			pagination: true
		}
	} );
} )( window.wp );
