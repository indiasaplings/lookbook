var config = {
	map: {
        '*': {
            magiccartLookbook: 'Magiccart_Lookbook/js/lookbook'
        }
    },
	paths: {
		'magiccart/gridtab'		: 'Magiccart_Lookbook/js/gridtab.min',
	},
	shim: {
		'magiccart/gridtab': {
			deps: ['jquery']
		},
	},
	
};
