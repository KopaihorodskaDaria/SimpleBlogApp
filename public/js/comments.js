    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.btn-comment-toggle').forEach(button => {
            button.addEventListener('click', async () => {
                const postId = button.getAttribute('data-post-id');
                const panel = document.getElementById('comment-panel-' + postId);

                if (panel.style.display === 'none') {
                    const response = await fetch('/comments/' + postId);
                    const html = await response.text();
                    panel.innerHTML = html;
                    panel.style.display = 'block';
                } else {
                    panel.style.display = 'none';
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.btn-edit-comment').forEach(button => {
            button.addEventListener('click', () => {
                const commentDiv = button.closest('.comment');
                commentDiv.querySelector('.comment-content').style.display = 'none';
                commentDiv.querySelector('.edit-comment-area').style.display = 'block';
                commentDiv.querySelector('.btn-save-comment').style.display = 'inline-block';
                button.style.display = 'none';
            });
        });

    document.querySelectorAll('.btn-save-comment').forEach(button => {
        button.addEventListener('click', async () => {
            const commentDiv = button.closest('.comment');
            const commentId = commentDiv.dataset.commentId;
            const newContent = commentDiv.querySelector('.edit-comment-area').value;

            const response = await fetch(`/comment/${commentId}/edit-ajax`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ content: newContent })
            });
            const data = await response.json();

            if (data.success) {
                commentDiv.querySelector('.comment-content').textContent = data.content;
                commentDiv.querySelector('.comment-content').style.display = 'inline';
                commentDiv.querySelector('.edit-comment-area').style.display = 'none';
                commentDiv.querySelector('.btn-edit-comment').style.display = 'inline-block';
                button.style.display = 'none';
            } else {
                alert(data.error || 'Error');
            }
        });
    });
});

