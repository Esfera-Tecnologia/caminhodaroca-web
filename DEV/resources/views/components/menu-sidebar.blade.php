<div class="list-group list-group-flush">

  @foreach ($menus as $menu)
    @php
      $routeName = $menu->slug === 'dashboard' ? 'dashboard' : $menu->slug . '.index';

      // Verifica permissão de visualização
      $canView = auth()->user()
          ->accessProfile
          ->permissions
          ->firstWhere('menu_id', $menu->id)
          ?->can_view;
    @endphp

    @if ($canView)
      <a href="{{ route($routeName) }}"
         class="list-group-item list-group-item-action {{ request()->routeIs($menu->slug . '.*') ? 'active' : '' }}">
        <i class="{{ $menu->icone }} me-2"></i> {{ $menu->nome }}
      </a>
    @endif
  @endforeach
</div>
