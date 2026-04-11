import defaultTheme from 'tailwindcss/defaultTheme'

export default {
    theme: {
        extend: {
            fontFamily: {
                body: ['Crimson Pro', ...defaultTheme.fontFamily.serif],
                heading: ['Hanken Grotesk', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                washi: '#f7f5f0',
                sumi: '#1a1a1a',
                matcha: '#7c8c5a',
                hai: '#888888',
                usuzumi: '#b4b4b4',
                usuzemi: '#5a6b6a',
                kuro: '#0a0a0a',
                take: '#4a9d6f',
                sakura: '#d97781',
                indigo: '#6366f1',
                shironeri: '#fefcf8',
            },
        },
    },
}
