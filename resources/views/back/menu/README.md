# Menu Back Office

Chaque module ajoute **son propre fichier** ici (ex. `coupures.blade.php`) : il est inclus
automatiquement dans la sidebar admin (ordre alphabétique des noms de fichiers).

```blade
<li class="nav-item">
    <a class="nav-link @if (request()->routeIs('admin.coupures.*')) active @endif" href="{{ route('admin.coupures.index') }}">
        <i class="bi bi-lightning-charge"></i> Coupures
    </a>
</li>
```
