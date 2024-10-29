import { Application } from 'stimulus';
import Carousel from 'stimulus-carousel';

const app = Application.start();

const context = require.context('./js/controllers', true, /_controller\.js$/);
context.keys().forEach((filename) => {
    const controllerModule = context(filename);
    const controllerName = filename.replace('./', '').replace('_controller.js', '').replace(/\//g, '--');
    app.register(controllerName, controllerModule.default || controllerModule);
});

app.register('carousel', Carousel);

export { app };
