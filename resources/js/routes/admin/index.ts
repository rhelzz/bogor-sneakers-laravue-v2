import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import carouselHomeA7ebd0 from './carousel-home'
import galeriKarya787fef from './galeri-karya'
import tiktokFeed38f9ec from './tiktok-feed'
import katalogCe320b from './katalog'
import preOrderHome1a388a from './pre-order-home'
import whatsappAdmins183620 from './whatsapp-admins'
import modelSepatu9e70d3 from './model-sepatu'
import shoeTypes from './shoe-types'
import variants from './variants'
import ordersB47e5f from './orders'
/**
* @see \App\Http\Controllers\DashboardController::dashboard
 * @see app/Http/Controllers/DashboardController.php:12
 * @route '/admin'
 */
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/admin',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DashboardController::dashboard
 * @see app/Http/Controllers/DashboardController.php:12
 * @route '/admin'
 */
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::dashboard
 * @see app/Http/Controllers/DashboardController.php:12
 * @route '/admin'
 */
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\DashboardController::dashboard
 * @see app/Http/Controllers/DashboardController.php:12
 * @route '/admin'
 */
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\DashboardController::dashboard
 * @see app/Http/Controllers/DashboardController.php:12
 * @route '/admin'
 */
    const dashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: dashboard.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\DashboardController::dashboard
 * @see app/Http/Controllers/DashboardController.php:12
 * @route '/admin'
 */
        dashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: dashboard.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\DashboardController::dashboard
 * @see app/Http/Controllers/DashboardController.php:12
 * @route '/admin'
 */
        dashboardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: dashboard.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    dashboard.form = dashboardForm
/**
* @see \App\Http\Controllers\CarouselSlideController::carouselHome
 * @see app/Http/Controllers/CarouselSlideController.php:15
 * @route '/admin/carousel-home'
 */
export const carouselHome = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: carouselHome.url(options),
    method: 'get',
})

carouselHome.definition = {
    methods: ["get","head"],
    url: '/admin/carousel-home',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CarouselSlideController::carouselHome
 * @see app/Http/Controllers/CarouselSlideController.php:15
 * @route '/admin/carousel-home'
 */
carouselHome.url = (options?: RouteQueryOptions) => {
    return carouselHome.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CarouselSlideController::carouselHome
 * @see app/Http/Controllers/CarouselSlideController.php:15
 * @route '/admin/carousel-home'
 */
carouselHome.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: carouselHome.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CarouselSlideController::carouselHome
 * @see app/Http/Controllers/CarouselSlideController.php:15
 * @route '/admin/carousel-home'
 */
carouselHome.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: carouselHome.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CarouselSlideController::carouselHome
 * @see app/Http/Controllers/CarouselSlideController.php:15
 * @route '/admin/carousel-home'
 */
    const carouselHomeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: carouselHome.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CarouselSlideController::carouselHome
 * @see app/Http/Controllers/CarouselSlideController.php:15
 * @route '/admin/carousel-home'
 */
        carouselHomeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: carouselHome.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CarouselSlideController::carouselHome
 * @see app/Http/Controllers/CarouselSlideController.php:15
 * @route '/admin/carousel-home'
 */
        carouselHomeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: carouselHome.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    carouselHome.form = carouselHomeForm
/**
* @see \App\Http\Controllers\GalleryKaryaController::galeriKarya
 * @see app/Http/Controllers/GalleryKaryaController.php:36
 * @route '/admin/galeri-karya'
 */
export const galeriKarya = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: galeriKarya.url(options),
    method: 'get',
})

galeriKarya.definition = {
    methods: ["get","head"],
    url: '/admin/galeri-karya',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GalleryKaryaController::galeriKarya
 * @see app/Http/Controllers/GalleryKaryaController.php:36
 * @route '/admin/galeri-karya'
 */
galeriKarya.url = (options?: RouteQueryOptions) => {
    return galeriKarya.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GalleryKaryaController::galeriKarya
 * @see app/Http/Controllers/GalleryKaryaController.php:36
 * @route '/admin/galeri-karya'
 */
galeriKarya.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: galeriKarya.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\GalleryKaryaController::galeriKarya
 * @see app/Http/Controllers/GalleryKaryaController.php:36
 * @route '/admin/galeri-karya'
 */
galeriKarya.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: galeriKarya.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\GalleryKaryaController::galeriKarya
 * @see app/Http/Controllers/GalleryKaryaController.php:36
 * @route '/admin/galeri-karya'
 */
    const galeriKaryaForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: galeriKarya.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\GalleryKaryaController::galeriKarya
 * @see app/Http/Controllers/GalleryKaryaController.php:36
 * @route '/admin/galeri-karya'
 */
        galeriKaryaForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: galeriKarya.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\GalleryKaryaController::galeriKarya
 * @see app/Http/Controllers/GalleryKaryaController.php:36
 * @route '/admin/galeri-karya'
 */
        galeriKaryaForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: galeriKarya.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    galeriKarya.form = galeriKaryaForm
/**
* @see \App\Http\Controllers\TikTokFeedController::tiktokFeed
 * @see app/Http/Controllers/TikTokFeedController.php:23
 * @route '/admin/tiktok-feed'
 */
export const tiktokFeed = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tiktokFeed.url(options),
    method: 'get',
})

tiktokFeed.definition = {
    methods: ["get","head"],
    url: '/admin/tiktok-feed',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TikTokFeedController::tiktokFeed
 * @see app/Http/Controllers/TikTokFeedController.php:23
 * @route '/admin/tiktok-feed'
 */
tiktokFeed.url = (options?: RouteQueryOptions) => {
    return tiktokFeed.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TikTokFeedController::tiktokFeed
 * @see app/Http/Controllers/TikTokFeedController.php:23
 * @route '/admin/tiktok-feed'
 */
tiktokFeed.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tiktokFeed.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\TikTokFeedController::tiktokFeed
 * @see app/Http/Controllers/TikTokFeedController.php:23
 * @route '/admin/tiktok-feed'
 */
tiktokFeed.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: tiktokFeed.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\TikTokFeedController::tiktokFeed
 * @see app/Http/Controllers/TikTokFeedController.php:23
 * @route '/admin/tiktok-feed'
 */
    const tiktokFeedForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: tiktokFeed.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\TikTokFeedController::tiktokFeed
 * @see app/Http/Controllers/TikTokFeedController.php:23
 * @route '/admin/tiktok-feed'
 */
        tiktokFeedForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: tiktokFeed.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\TikTokFeedController::tiktokFeed
 * @see app/Http/Controllers/TikTokFeedController.php:23
 * @route '/admin/tiktok-feed'
 */
        tiktokFeedForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: tiktokFeed.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    tiktokFeed.form = tiktokFeedForm
/**
* @see \App\Http\Controllers\KatalogController::katalog
 * @see app/Http/Controllers/KatalogController.php:63
 * @route '/admin/katalog'
 */
export const katalog = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: katalog.url(options),
    method: 'get',
})

katalog.definition = {
    methods: ["get","head"],
    url: '/admin/katalog',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\KatalogController::katalog
 * @see app/Http/Controllers/KatalogController.php:63
 * @route '/admin/katalog'
 */
katalog.url = (options?: RouteQueryOptions) => {
    return katalog.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\KatalogController::katalog
 * @see app/Http/Controllers/KatalogController.php:63
 * @route '/admin/katalog'
 */
katalog.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: katalog.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\KatalogController::katalog
 * @see app/Http/Controllers/KatalogController.php:63
 * @route '/admin/katalog'
 */
katalog.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: katalog.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\KatalogController::katalog
 * @see app/Http/Controllers/KatalogController.php:63
 * @route '/admin/katalog'
 */
    const katalogForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: katalog.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\KatalogController::katalog
 * @see app/Http/Controllers/KatalogController.php:63
 * @route '/admin/katalog'
 */
        katalogForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: katalog.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\KatalogController::katalog
 * @see app/Http/Controllers/KatalogController.php:63
 * @route '/admin/katalog'
 */
        katalogForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: katalog.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    katalog.form = katalogForm
/**
* @see \App\Http\Controllers\PreOrderHomeController::preOrderHome
 * @see app/Http/Controllers/PreOrderHomeController.php:23
 * @route '/admin/pre-order-home'
 */
export const preOrderHome = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: preOrderHome.url(options),
    method: 'get',
})

preOrderHome.definition = {
    methods: ["get","head"],
    url: '/admin/pre-order-home',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PreOrderHomeController::preOrderHome
 * @see app/Http/Controllers/PreOrderHomeController.php:23
 * @route '/admin/pre-order-home'
 */
preOrderHome.url = (options?: RouteQueryOptions) => {
    return preOrderHome.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PreOrderHomeController::preOrderHome
 * @see app/Http/Controllers/PreOrderHomeController.php:23
 * @route '/admin/pre-order-home'
 */
preOrderHome.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: preOrderHome.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PreOrderHomeController::preOrderHome
 * @see app/Http/Controllers/PreOrderHomeController.php:23
 * @route '/admin/pre-order-home'
 */
preOrderHome.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: preOrderHome.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PreOrderHomeController::preOrderHome
 * @see app/Http/Controllers/PreOrderHomeController.php:23
 * @route '/admin/pre-order-home'
 */
    const preOrderHomeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: preOrderHome.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PreOrderHomeController::preOrderHome
 * @see app/Http/Controllers/PreOrderHomeController.php:23
 * @route '/admin/pre-order-home'
 */
        preOrderHomeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: preOrderHome.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PreOrderHomeController::preOrderHome
 * @see app/Http/Controllers/PreOrderHomeController.php:23
 * @route '/admin/pre-order-home'
 */
        preOrderHomeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: preOrderHome.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    preOrderHome.form = preOrderHomeForm
/**
* @see \App\Http\Controllers\WhatsAppAdminController::whatsappAdmins
 * @see app/Http/Controllers/WhatsAppAdminController.php:15
 * @route '/admin/whatsapp-admins'
 */
export const whatsappAdmins = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: whatsappAdmins.url(options),
    method: 'get',
})

whatsappAdmins.definition = {
    methods: ["get","head"],
    url: '/admin/whatsapp-admins',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WhatsAppAdminController::whatsappAdmins
 * @see app/Http/Controllers/WhatsAppAdminController.php:15
 * @route '/admin/whatsapp-admins'
 */
whatsappAdmins.url = (options?: RouteQueryOptions) => {
    return whatsappAdmins.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatsAppAdminController::whatsappAdmins
 * @see app/Http/Controllers/WhatsAppAdminController.php:15
 * @route '/admin/whatsapp-admins'
 */
whatsappAdmins.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: whatsappAdmins.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\WhatsAppAdminController::whatsappAdmins
 * @see app/Http/Controllers/WhatsAppAdminController.php:15
 * @route '/admin/whatsapp-admins'
 */
whatsappAdmins.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: whatsappAdmins.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\WhatsAppAdminController::whatsappAdmins
 * @see app/Http/Controllers/WhatsAppAdminController.php:15
 * @route '/admin/whatsapp-admins'
 */
    const whatsappAdminsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: whatsappAdmins.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\WhatsAppAdminController::whatsappAdmins
 * @see app/Http/Controllers/WhatsAppAdminController.php:15
 * @route '/admin/whatsapp-admins'
 */
        whatsappAdminsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: whatsappAdmins.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\WhatsAppAdminController::whatsappAdmins
 * @see app/Http/Controllers/WhatsAppAdminController.php:15
 * @route '/admin/whatsapp-admins'
 */
        whatsappAdminsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: whatsappAdmins.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    whatsappAdmins.form = whatsappAdminsForm
/**
* @see \App\Http\Controllers\ShoeModelController::modelSepatu
 * @see app/Http/Controllers/ShoeModelController.php:18
 * @route '/admin/model-sepatu'
 */
export const modelSepatu = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: modelSepatu.url(options),
    method: 'get',
})

modelSepatu.definition = {
    methods: ["get","head"],
    url: '/admin/model-sepatu',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ShoeModelController::modelSepatu
 * @see app/Http/Controllers/ShoeModelController.php:18
 * @route '/admin/model-sepatu'
 */
modelSepatu.url = (options?: RouteQueryOptions) => {
    return modelSepatu.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ShoeModelController::modelSepatu
 * @see app/Http/Controllers/ShoeModelController.php:18
 * @route '/admin/model-sepatu'
 */
modelSepatu.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: modelSepatu.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ShoeModelController::modelSepatu
 * @see app/Http/Controllers/ShoeModelController.php:18
 * @route '/admin/model-sepatu'
 */
modelSepatu.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: modelSepatu.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ShoeModelController::modelSepatu
 * @see app/Http/Controllers/ShoeModelController.php:18
 * @route '/admin/model-sepatu'
 */
    const modelSepatuForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: modelSepatu.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ShoeModelController::modelSepatu
 * @see app/Http/Controllers/ShoeModelController.php:18
 * @route '/admin/model-sepatu'
 */
        modelSepatuForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: modelSepatu.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ShoeModelController::modelSepatu
 * @see app/Http/Controllers/ShoeModelController.php:18
 * @route '/admin/model-sepatu'
 */
        modelSepatuForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: modelSepatu.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    modelSepatu.form = modelSepatuForm
/**
* @see \App\Http\Controllers\OrderController::orders
 * @see app/Http/Controllers/OrderController.php:21
 * @route '/admin/orders'
 */
export const orders = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: orders.url(options),
    method: 'get',
})

orders.definition = {
    methods: ["get","head"],
    url: '/admin/orders',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OrderController::orders
 * @see app/Http/Controllers/OrderController.php:21
 * @route '/admin/orders'
 */
orders.url = (options?: RouteQueryOptions) => {
    return orders.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\OrderController::orders
 * @see app/Http/Controllers/OrderController.php:21
 * @route '/admin/orders'
 */
orders.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: orders.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\OrderController::orders
 * @see app/Http/Controllers/OrderController.php:21
 * @route '/admin/orders'
 */
orders.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: orders.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\OrderController::orders
 * @see app/Http/Controllers/OrderController.php:21
 * @route '/admin/orders'
 */
    const ordersForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: orders.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\OrderController::orders
 * @see app/Http/Controllers/OrderController.php:21
 * @route '/admin/orders'
 */
        ordersForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: orders.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\OrderController::orders
 * @see app/Http/Controllers/OrderController.php:21
 * @route '/admin/orders'
 */
        ordersForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: orders.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    orders.form = ordersForm
const admin = {
    dashboard: Object.assign(dashboard, dashboard),
carouselHome: Object.assign(carouselHome, carouselHomeA7ebd0),
galeriKarya: Object.assign(galeriKarya, galeriKarya787fef),
tiktokFeed: Object.assign(tiktokFeed, tiktokFeed38f9ec),
katalog: Object.assign(katalog, katalogCe320b),
preOrderHome: Object.assign(preOrderHome, preOrderHome1a388a),
whatsappAdmins: Object.assign(whatsappAdmins, whatsappAdmins183620),
modelSepatu: Object.assign(modelSepatu, modelSepatu9e70d3),
shoeTypes: Object.assign(shoeTypes, shoeTypes),
variants: Object.assign(variants, variants),
orders: Object.assign(orders, ordersB47e5f),
}

export default admin