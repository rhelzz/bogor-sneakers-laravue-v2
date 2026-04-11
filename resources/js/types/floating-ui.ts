export type FloatingNavPage = 'home' | 'katalog' | 'studio-custom' | 'tracking'

export interface FloatingContact {
    id: number | string
    name: string
    role: string
    phone: string
    initial: string
    color: string
}

export interface FloatingOrder {
    id: string
    product: string
    size: string
    status: string
    statusClass: string
    progress: number
    progressClass: string
}
