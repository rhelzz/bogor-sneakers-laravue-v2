export interface TikTokFeedItem {
  id: number
  url: string
  category: string
  title: string | null
  author_name: string | null
  thumbnail_url: string | null
  video_id: string | null
  is_active?: boolean
  sort_order?: number
  created_at?: string
  updated_at?: string
}

export interface TikTokFollowerSnapshot {
  username: string
  followers: number | null
  formatted_followers: string
  source: string
  last_updated: string | null
  is_stale: boolean
}
