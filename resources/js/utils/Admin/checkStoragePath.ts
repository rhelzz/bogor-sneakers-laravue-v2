/**
 * Debug utility untuk check storage path dan image URL
 */

export const debugImagePath = (slide: any) => {
  console.group('🔍 Image Path Debug');

  console.log('Slide Object:', {
    id: slide.id,
    image_path: slide.image_path,
    image_url: slide.image_url,
    created_at: slide.created_at,
  });

  // Check if URL is valid
  const isAbsolute = slide.image_url?.startsWith('/') || slide.image_url?.startsWith('http');
  console.log('Is absolute URL:', isAbsolute);
  console.log('URL format check:', {
    startsWithStorage: slide.image_url?.startsWith('/storage'),
    isHttp: slide.image_url?.startsWith('http'),
  });

  // Expected path pattern
  const expectedPattern = /\/storage\/carousel\/[\w-]+\.(jpg|jpeg|png)$/i;
  const matches = expectedPattern.test(slide.image_url);
  console.log('Matches expected pattern (/storage/carousel/*):', matches);

  // Direct fetch test
  console.log('Testing fetch to:', slide.image_url);
  fetch(slide.image_url, { method: 'HEAD' })
    .then((res) => {
      console.log('✅ Image accessible:', {
        status: res.status,
        contentType: res.headers.get('content-type'),
      });
    })
    .catch((err) => {
      console.error('❌ Image NOT accessible:', err.message);
    });

  console.groupEnd();
};

export const debugStorageSymlink = () => {
  console.group('🔗 Storage Symlink Check');

  // Check if /storage exists and is accessible
  const testUrl = '/storage/carousel/';
  fetch(testUrl, { method: 'HEAD' })
    .then((res) => {
      console.log('✅ /storage path exists:', res.status);
    })
    .catch((err) => {
      console.error('❌ /storage path MISSING:', err.message);
      console.warn('Fix: Run `php artisan storage:link` in server terminal');
    });

  console.groupEnd();
};
