<div>
    <h1>Добро пожаловать, {{ $user->name }}!</h1>
    <p>Спасибо за регистрацию!</p>
    <p>Ваш email: {{ $user->email }}</p>
    <p>Дата регистрации: {{ $user->created_at->format('d.m.Y') }}</p>
</div>
