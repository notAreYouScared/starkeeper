<style>
    .sk-discord-divider-line { border-top: 1px solid #d1d5db; }
    .sk-discord-divider-label { background: #fff; color: #6b7280; }
    .dark .sk-discord-divider-line { border-top: 1px solid #4b5563; }
    .dark .sk-discord-divider-label { background: rgb(31 41 55); color: #9ca3af; }
</style>

<div style="margin-top:1rem;">
    <div style="position:relative;margin-top:1rem;margin-bottom:1rem;">
        <div style="position:absolute;inset:0;display:flex;align-items:center;">
            <div class="sk-discord-divider-line" style="width:100%;"></div>
        </div>
        <div style="position:relative;display:flex;justify-content:center;font-size:0.875rem;">
            <span class="sk-discord-divider-label" style="padding:0 0.5rem;">or continue with</span>
        </div>
    </div>
    <a href="{{ route('auth.discord.redirect') }}"
       style="display:flex;align-items:center;justify-content:center;gap:0.625rem;width:100%;padding:0.625rem 1rem;border-radius:0.5rem;font-size:0.875rem;font-weight:600;color:#ffffff;background-color:#5865F2;text-decoration:none;border:none;cursor:pointer;transition:background-color 0.15s ease;"
       onmouseover="this.style.backgroundColor='#4752c4'"
       onmouseout="this.style.backgroundColor='#5865F2'">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 127.14 96.36" width="20" height="20" fill="currentColor" style="flex-shrink:0;">
            <path d="M107.7,8.07A105.15,105.15,0,0,0,81.47,0a72.06,72.06,0,0,0-3.36,6.83A97.68,97.68,0,0,0,49,6.83,72.37,72.37,0,0,0,45.64,0,105.89,105.89,0,0,0,19.39,8.09C2.79,32.65-1.71,56.6.54,80.21h0A105.73,105.73,0,0,0,32.71,96.36,77.7,77.7,0,0,0,39.6,85.25a68.42,68.42,0,0,1-10.85-5.18c.91-.66,1.8-1.34,2.66-2a75.57,75.57,0,0,0,64.32,0c.87.71,1.76,1.39,2.66,2a68.68,68.68,0,0,1-10.87,5.19,77,77,0,0,0,6.89,11.1A105.25,105.25,0,0,0,126.6,80.22h0C129.24,52.84,122.09,29.11,107.7,8.07ZM42.45,65.69C36.18,65.69,31,60,31,53s5-12.74,11.43-12.74S54,46,53.89,53,48.84,65.69,42.45,65.69Zm42.24,0C78.41,65.69,73.25,60,73.25,53s5-12.74,11.44-12.74S96.23,46,96.12,53,91.08,65.69,84.69,65.69Z"/>
        </svg>
        Sign in with Discord
    </a>
</div>
