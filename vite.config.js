import { createWpViteConfig } from 'pressbooks-build-tools';
import { resolve } from 'path';
import tailwindcss from 'tailwindcss';

export default createWpViteConfig({
	input: {
		app: resolve(__dirname, 'assets/js/app.js'),
	},
	outDir: 'assets/dist',
	useTailwind: true,
	tailwindcss,
});
