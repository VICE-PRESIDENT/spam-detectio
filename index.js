document.getElementById('fetchButton').addEventListener('click', async function () {
    const videoUrl = document.getElementById('videoUrl').value;
    if (!videoUrl) {
        alert('Please enter a valid YouTube URL.');
        return;
    }

    let videoId;
    try {
        videoId = new URL(videoUrl).searchParams.get('v');
        if (!videoId) throw new Error();
    } catch {
        alert('Invalid YouTube URL. Please make sure it includes a valid video ID.');
        return;
    }

    console.log('Video ID:', videoId);

    try {
        const fetchResponse = await fetch('fetch_comments.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `video_id=${videoId}`
        });

        if (!fetchResponse.ok) throw new Error(`HTTP error! status: ${fetchResponse.status}`);
        const fetchResult = await fetchResponse.json();
        console.log('Fetch result:', fetchResult);

        if (fetchResult.success) {
            const analyzeResponse = await fetch('analyze_comments.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `video_id=${videoId}`
            });

            if (!analyzeResponse.ok) throw new Error(`HTTP error! status: ${analyzeResponse.status}`);
            const analyzeResult = await analyzeResponse.json();
            console.log('Analyze result:', analyzeResult);

            if (analyzeResult.success) {
                const commentsResponse = await fetch(`fetch_comments_list.php?video_id=${videoId}`);
                const comments = await commentsResponse.json();
                console.log('Comments:', comments);

                // Check if comments are retrieved correctly
                if (comments.error) {
                    alert(comments.error);
                } else {
                    const tableBody = document.getElementById('commentsTable');
                    tableBody.innerHTML = '';
                    comments.forEach((comment, index) => {
                        const row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${comment.author}</td>
                                <td>${comment.text}</td>
                                <td>${comment.is_spam ? 'Yes' : 'No'}</td>
                            </tr>`;
                        tableBody.innerHTML += row;
                    });
                }
            }
        } else {
            alert('Failed to fetch comments. Please check the video ID and try again.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while fetching or analyzing comments.');
    }
});