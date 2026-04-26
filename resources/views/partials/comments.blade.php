<section class="sec" id="comments" style="padding-top:16px;padding-bottom:64px;">
  <div class="comment-form-card rv">
    <div class="comment-form-stripe"></div>
    <div class="comment-form-header">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      <span>نظر شما درباره این تحلیل</span>
    </div>
    <p class="comment-form-sub">دیدگاه و تجربه بالینی خود را با ما به اشتراک بگذارید</p>

    <div id="commentSuccess" style="display:none;padding:14px;background:#f0fdf4;border-radius:10px;border:1px solid #bbf7d0;color:#166534;font-size:0.88rem;margin-bottom:14px;">
      نظر شما با موفقیت ثبت شد. ممنون!
    </div>

    <div id="commentError" style="display:none;padding:14px;background:#fef2f2;border-radius:10px;border:1px solid #fecaca;color:#991b1b;font-size:0.88rem;margin-bottom:14px;">
      خطا در ثبت نظر. لطفاً دوباره امتحان کنید.
    </div>

    <form id="commentForm">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="episode_id" value="{{ $episode->id }}">
      <div class="comment-form-row">
        <input type="text" name="name" placeholder="نام شما" required class="comment-input">
      </div>
      <textarea name="body" placeholder="نظر خود را بنویسید..." required rows="4" class="comment-textarea"></textarea>
      <button type="submit" class="comment-submit" id="commentBtn">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9"/></svg>
        ارسال نظر
      </button>
    </form>
  </div>

  <div class="comments-count rv">{{ $episode->comments->count() }} نظر</div>

  <div class="comments-list">
    @foreach($episode->comments as $comment)
    <div class="comment-card rv">
      <div class="comment-header">
        <div class="comment-avatar" style="background:{{ $loop->index % 2 == 0 ? '#fef2f2' : '#f0fdfa' }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="{{ $loop->index % 2 == 0 ? '#e11d48' : '#0d9488' }}" stroke-width="1.8"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
        <div>
          <div class="comment-name">{{ $comment->name }}</div>
          <div class="comment-date">{{ $comment->time_ago }}</div>
        </div>
      </div>
      <div class="comment-body">{{ $comment->body }}</div>
    </div>
    @endforeach
  </div>

</section>

<script>
document.getElementById('commentForm').addEventListener('submit', function(e) {
  e.preventDefault();
  var btn = document.getElementById('commentBtn');
  var form = this;
  btn.disabled = true;
  btn.textContent = 'در حال ارسال...';

  fetch('/comment', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': form.querySelector('[name=_token]').value,
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      episode_id: form.querySelector('[name=episode_id]').value,
      name: form.querySelector('[name=name]').value,
      body: form.querySelector('[name=body]').value
    })
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.success) {
      document.getElementById('commentSuccess').style.display = 'block';
      document.getElementById('commentError').style.display = 'none';
      form.querySelector('[name=name]').value = '';
      form.querySelector('[name=body]').value = '';
      setTimeout(function(){ location.reload(); }, 1500);
    } else {
      document.getElementById('commentError').style.display = 'block';
    }
    btn.disabled = false;
    btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9"/></svg> ارسال نظر';
  })
  .catch(function() {
    document.getElementById('commentError').style.display = 'block';
    btn.disabled = false;
    btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9"/></svg> ارسال نظر';
  });
});
</script>
