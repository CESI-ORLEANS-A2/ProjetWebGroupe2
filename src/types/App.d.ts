/// <reference path="Icons.d.ts" />
/// <reference path="Javascript.d.ts" />

declare type InitComponentFunction = (this: App, instance: ComponentInstance) => void;
declare type InitPluginFunction = (this: App, instance: PluginInstance) => void;
declare type CSSObjectType = { [selector: string]: CSSObjectInlineType };
declare type CSSObjectInlineType = { [keyword: string]: string | number };

declare interface Component {
	name: string;
	fullName: string;
	instances: Array<ComponentInstance>;
	init: (instance: ComponentInstance) => void;
	defaultData?: any;
}

declare interface ComponentInstance {
	components: Array<Component>;
	element: HTMLElement;
	data?: any;
}

declare interface PluginInstance {
	plugins: Array<Component>;
	data?: any;
}

declare type ToastOptions = {
	duration: number;
	filled: boolean;
};
declare type ToastFullOptions = ToastOptions & {
	type: string;
};
declare type ToastResult = {
	element: HTMLDivElement;
	close: () => void;
	pause: () => void;
	resume: () => void;
};

declare type DialogOptions = {
	title: string;
	content: string;
	parseHTML: boolean;
};
declare type DialogFullOptions = DialogOptions & {
	buttons?: Array<DialogButton>;
};
declare type DialogButton = {
	text: string;
	icon?: IconKeywords;
	color?: string;
	action?: 'close' | ((close: () => void) => void);
	filled?: boolean;
};
declare type DialogResult = {
	element: HTMLDivElement;
	close: () => void;
};

declare type ComponentsArgsMap = {
	select: {
		id: string;
		icon?: IconKeywords;
		iconBefore?: IconKeywords;
		prefix?: string;
		multiple?: boolean;
		options?: [{ text: string; value: string; selected?: boolean; disabled?: boolean }];
		noCurrentValue?: boolean;
		allowEmpty?: boolean;
	};
	textarea: {
		id: string;
		label: string;
		icon?: IconKeywords;
		name: string;
		value: string;
		placeholder: string;
		required: boolean;
		multiline: boolean;
	};
	button: {
		id: string;
		text: string;
		icon?: IconKeywords;
		iconBefore?: IconKeywords;
		iconAfter?: IconKeywords;
	};
	skeleton: {
		styles?: Array<'rounded' | 'rounded-lg' | 'rounded-full' | 'circle' | 'square'>;
		width?: string;
		height?: string;
	};
};

declare type JSONResponse = {
	status: number;
	body: any;
};

declare class AppRequest {
	get(url: string, options?: any): Promise<JSONResponse>;
	post(url: string, options?: any): Promise<JSONResponse>;
	put(url: string, options?: any): Promise<JSONResponse>;
	delete(url: string, options?: any): Promise<JSONResponse>;
	patch(url: string, options?: any): Promise<JSONResponse>;
	head(url: string, options?: any): Promise<JSONResponse>;
	options(url: string, options?: any): Promise<JSONResponse>;
	connect(url: string, options?: any): Promise<JSONResponse>;
	trace(url: string, options?: any): Promise<JSONResponse>;
	request(options: any): Promise<JSONResponse>;
}

declare type TooltipProxy = {
	element: HTMLDivElement & {
		__tooltip: TooltipProxy;
	};
	content: string | HTMLElement | Array<string | HTMLElement>;
	parseHTML: boolean;
	id: number;
	position: 'top' | 'bottom' | 'left' | 'right';
	target: HTMLElement;
	offset: number;
	styled: boolean;
};

declare class App {
	components: {
		[key: string]: Array<Component>;
	};

	registerComponent(name: string, init: InitComponentFunction): Component;
	createComponent<K extends keyof ComponentsArgsMap>(
		name: K,
		args: ComponentsArgsMap[K]
	): Component;
	initAllComponentElements(name: string): Component;
	initComponentElement(name: string, el: HTMLElement): Component;

	registerPlugin(name: string, init: InitPluginFunction): PluginInstance;

	watchVariable(
		variable: any,
		callback: (props: string | number, oldValue: any, newValue: any, obj: any) => void
	): void;

	createElement<K extends keyof HTMLElementTagNameMap>(
		type: K,
		attributes: {
			on?: { [eventName: string]: Function };
			once?: { [eventName: string]: Function };
			style?: { [styleName: string]: string };
			[attributeName: string]: any;
		},
		...children: Array<HTMLElement | string>
	): HTMLElementTagNameMap[K];
	createIcon(name: IconKeywords): HTMLSpanElement;

	stringifyCSS(styles: CSSObjectType): string;
	stringifyInlineCSS(styles: CSSObjectInlineType): string;
	injectCSS(css: CSSObjectType): HTMLStyleElement;
	camelToKebab(str: string): string;
	parseHtml(html: string): HTMLElement | Array<HTMLElement>;

	dispatchEvent(
		element: HTMLElement | string | Array<HTMLElement | string>,
		eventName: string,
		detail?: any
	): void;

	toast?: {
		info(message: string, options: ToastOptions): ToastResult;
		success(message: string, options: ToastOptions): ToastResult;
		warning(message: string, options: ToastOptions): ToastResult;
		error(message: string, options: ToastOptions): ToastResult;
		create(message: string, options: ToastFullOptions): ToastResult;
	};

	dialog?: {
		create(options: DialogOptions): DialogResult;
		confirm(options: DialogOptions): DialogResult;
	};

	tooltip?: {
		add(
			target: HTMLElement,
			content: string | HTMLElement | Array<string | HTMLElement>,
			{
				parseHTML = false,
				position = 'top',
				offset = 0,
				styled = true,
				align = 'center',
				fullWidth = false,
				appendTo = document.body,
			}: {
				parseHTML?: boolean;
				position?: 'top' | 'bottom' | 'left' | 'right';
				offset?: number;
				styled?: boolean;
				align?: 'border' | 'center';
				fullWidth?: boolean;
				appendTo?: HTMLElement;
			} = {}
		): TooltipProxy;
	};

	request: AppRequest;
}

declare const app: App;
