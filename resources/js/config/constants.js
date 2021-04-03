let origin = window.location.origin
// let ROUTE_TOKEN = process.env.ORG_SECRET_TOKEN ? process.env.ORG_SECRET_TOKEN : 'wzAh9mOfP5PdCx7VBqJBVuHLcEhblqmO2VV65JzLtsx1qjJyWuWC'

export const constants = {
    COOKIE_PREFIX: "__stlv2_",
    DRAWTIME_MORNING: process.env.MIX_DRAWTIME_MORNING,
    DRAWTIME_AFTERNOON: process.env.MIX_DRAWTIME_AFTERNOON,
    DRAWTIME_EVENING: process.env.MIX_DRAWTIME_EVENING,
    DRAWTIME_CUTOFF: process.env.MIX_DRAWTIME_CUTOFF,
};
