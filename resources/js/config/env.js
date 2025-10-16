const APP_URL = process.env.MIX_HOST;
const APP_KEY = process.env.MIX_API_KEY;
const GOOGLE_TOKEN = process.env.MIX_GOOGLE_MAP_KEY;
const APP_DEMO = process.env.MIX_DEMO;

const ENV = {
    API_URL: APP_URL,
    API_KEY: APP_KEY,
    GOOGLE_MAP_KEY: GOOGLE_TOKEN,
    DEMO: APP_DEMO
};
export default ENV;
