import app from 'flarum/forum/app';

app.initializers.add('leo/wechat-push', () => {
  console.log('[leo/wechat-push] Hello, forum!');
});
