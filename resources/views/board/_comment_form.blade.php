<div class="form-group w-auto">
    <label for="createBoardFormContent">本文</label>
    <textarea name="comment[content]" id="createBoardFormContent" cols="30" rows="10" class="form-control @error('comment.content') is-invalid @enderror">{{ old('comment.content') }}</textarea>
    @if($errors->has('comment.content'))
        <div class="invalid-feedback">
            {{ $errors->get('comment.content')[0] }}
        </div>
    @else
        <small id="commentContentHelp" class="form-text text-muted">5文字から3000文字以内で入力してください</small>
    @endif
</div>
