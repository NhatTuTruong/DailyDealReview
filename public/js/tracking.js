function getOrSetUKey() {
    const cookieName = 'track_ukey';
    let track_ukey = getCookie(cookieName);

    if (!track_ukey) {
        if (typeof crypto !== 'undefined' && crypto.randomUUID) {
            track_ukey = crypto.randomUUID();
        } else {
            track_ukey = generateUUIDv4();
        }
        document.cookie = cookieName + "=" + track_ukey + ";path=/;max-age=" + (30 * 24 * 60 * 60);
    }

    return track_ukey;
}


function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

function generateUUIDv4() {
    // Tạo UUID v4 thủ công
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        const r = Math.random() * 16 | 0;
        const v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}


$(document).ready(function () {
    const track_ukey = getOrSetUKey();
    $.ajax({
        url: '/api/record',
        method: 'POST',
        data: {
            track_ukey: track_ukey,
            request: window.location.href,
            referer: document.referrer,
            user_agent: navigator.userAgent
        },
        success: function (res) {
            console.log('Tracking done');
        }
    });
});