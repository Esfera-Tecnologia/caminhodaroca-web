<div class="list-group list-group-flush">

  @foreach ($menus as $menu)
    @php
      $routeName = $menu->slug === 'dashboard' ? 'dashboard' : $menu->slug . '.index';
    @endphp

    @if (getPermissao($menu->slug)?->can_view ?? false)
      <a href="{{ route($routeName) }}"
         class="list-group-item list-group-item-action {{ request()->routeIs($menu->slug . '.*')||request()->routeIs($menu->slug) ? 'active' : '' }}">
        <i class="{{ $menu->icone }} me-2"></i> {{ $menu->nome }}
       </a>
    @endif
  @endforeach
</div>
