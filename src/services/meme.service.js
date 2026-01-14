import axios from 'axios';

export async function searchMeme(query) {
    // 1. Try Google Custom Search API if keys exist
    if (process.env.GOOGLE_API_KEY && process.env.GOOGLE_CSE_ID) {
        try {
            const res = await axios.get('https://www.googleapis.com/customsearch/v1', {
                params: {
                    key: process.env.GOOGLE_API_KEY,
                    cx: process.env.GOOGLE_CSE_ID,
                    q: query,
                    searchType: 'image',
                    imgSize: 'large',
                    num: 10
                }
            });
            if (res.data.items && res.data.items.length > 0) {
                return res.data.items.map(item => item.link);
            }
        } catch (e) {
            console.error('Google API Error:', e.message);
        }
    }

    // 2. Fallback: Scrape Google Images (Basic - returns thumbnails/small images)
    try {
        const res = await axios.get(`https://www.google.com/search?tbm=isch&tbs=isz:l&q=${encodeURIComponent(query)}`, {
            headers: {
                'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            }
        });

        const imgUrls = [];
        const regex = /<img[^>]+src="([^">]+)"/g;
        let match;
        while ((match = regex.exec(res.data)) !== null) {
            if (match[1].startsWith('http')) {
                imgUrls.push(match[1]);
            }
        }

        const validUrls = imgUrls.filter(url => !url.includes('google.com/images/nav_logo'));

        if (validUrls.length > 0) {
            return validUrls;
        }
    } catch (e) {
        console.error('Scraping Error:', e.message);
    }

    return [];
}
