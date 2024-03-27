/// <reference path="../../static/js/base.js" />

app &&
	app.registerComponent('skeleton', null, function (component, args) {
		return app.createElement('div', {
			class: 'c-skeleton ' + (args.styles || ''),
			style:
				(args.width ? `--skeleton-width:${args.width};` : '') +
				(args.height ? `--skeleton-height:${args.height};` : '')
		});
	});
