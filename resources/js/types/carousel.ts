export interface CarouselSlide {
  id: number
  image_path: string
  image_url?: string
  is_active: boolean
  order?: number
  created_at?: string
  updated_at?: string
}

export interface CarouselApiResponse extends CarouselSlide {
  image_url: string
}
