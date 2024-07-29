import fs from 'fs';
import path from 'path';
import { Router } from 'express';
import { consoleLog } from '../helper/index.js';

export const addRoutesDynamically = async (app, routesPath, prefix = '') => {
    const files = await fs.promises.readdir(routesPath);
    const router = Router();
    for (const file of files) {
        const routeModule = await import(path.join(routesPath, file));
        const urlPrefix = prefix || '/';
        const routePrefix = urlPrefix === '/' ? '' : urlPrefix;
        const route = routeModule.default || routeModule;
        router.use(routePrefix, route);
    }
    app.use(router);
}
