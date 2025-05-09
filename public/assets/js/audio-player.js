window.AudioPlayer = class AudioPlayer {
    constructor() {
        // Estado inicial
        this.state = {
            initialized: false,
            loading: false,
            playing: false,
            volume: 100,
            muted: false
        };

        // Player e playlist
        this.player = null;
        this.playlist = [];
        this.currentTrack = null;
        this.currentIndex = 0;

        // Cache dos elementos DOM
        this.elements = this.getPlayerElements();

        // Configuração do player do YouTube
        this.playerConfig = {
            height: '0',
            width: '0',
            playerVars: {
                autoplay: 0,
                controls: 0,
                disablekb: 1,
                enablejsapi: 1,
                iv_load_policy: 3,
                modestbranding: 1,
                playsinline: 1,
                rel: 0,
                showinfo: 0,
                origin: window.location.origin
            },
            events: {
                onReady: this.handlePlayerReady.bind(this),
                onStateChange: this.handlePlayerStateChange.bind(this),
                onError: this.handlePlayerError.bind(this)
            }
        };

        // Inicializa o player
        this.init();
    }

    // Inicialização
    async init() {
        try {
            if (this.state.initialized || this.state.loading) return;
            this.state.loading = true;

            // Carrega a API do YouTube se necessário
            await this.loadYouTubeAPI();

            // Cria o player
            await this.createPlayer();

            // Configura eventos
            this.setupEventListeners();

            this.state.initialized = true;
            console.log('Player inicializado com sucesso');
        } catch (error) {
            console.error('Erro na inicialização do player:', error);
            this.showError('Erro ao inicializar o player. Tente recarregar a página.');
        } finally {
            this.state.loading = false;
        }
    }

    // Carrega a API do YouTube
    loadYouTubeAPI() {
        return new Promise((resolve) => {
            // Se a API já estiver carregada, retorna imediatamente
            if (window.YT && window.YT.Player) {
                console.log('API do YouTube já carregada');
                resolve();
                return;
            }

            // Define o callback global
            window.onYouTubeIframeAPIReady = () => {
                console.log('API do YouTube carregada com sucesso');
                this.state.youtubeAvailable = true;
                resolve();
            };

            try {
                // Carrega o script da API
                const tag = document.createElement('script');
                tag.src = 'https://www.youtube.com/iframe_api';
                
                // Em caso de erro, continua mesmo sem YouTube
                tag.onerror = () => {
                    console.warn('Não foi possível carregar a API do YouTube. Alguns recursos podem estar indisponíveis.');
                    this.state.youtubeAvailable = false;
                    resolve(); // Resolve mesmo com erro para não interromper o fluxo
                };
                
                document.head.appendChild(tag);

                // Timeout mais curto (5 segundos) para não travar a interface
                setTimeout(() => {
                    if (!window.YT || !window.YT.Player) {
                        console.warn('Timeout ao carregar a API do YouTube. Continuando sem este recurso.');
                        this.state.youtubeAvailable = false;
                        resolve(); // Resolve mesmo após timeout
                    }
                }, 5000);
            } catch (e) {
                console.warn('Erro ao tentar carregar a API do YouTube:', e);
                this.state.youtubeAvailable = false;
                resolve(); // Resolve mesmo com exceção
            }
        });
    }

    // Cria o player do YouTube
    createPlayer() {
        return new Promise((resolve) => {
            try {
                // Verifica se a API do YouTube está disponível
                if (!window.YT || !window.YT.Player || this.state.youtubeAvailable === false) {
                    console.warn('API do YouTube não disponível. Usando modo alternativo.');
                    this.state.youtubeAvailable = false;
                    this.setupAlternativePlayer();
                    resolve(); // Continua mesmo sem o YouTube
                    return;
                }
                
                if (!this.elements.youtubePlayer) {
                    console.warn('Elemento do player não encontrado. Usando modo alternativo.');
                    this.setupAlternativePlayer();
                    resolve(); // Continua mesmo sem o elemento
                    return;
                }

                // Cria o player do YouTube se tudo estiver disponível
                try {
                    this.player = new YT.Player('youtube-player', {
                        ...this.playerConfig,
                        events: {
                            ...this.playerConfig.events,
                            onReady: () => {
                                console.log('Player do YouTube pronto');
                                this.state.youtubeAvailable = true;
                                resolve();
                            },
                            onError: (error) => {
                                console.warn('Erro no player do YouTube:', error);
                                this.state.youtubeAvailable = false;
                                this.setupAlternativePlayer();
                                resolve(); // Continua mesmo com erro
                            }
                        }
                    });
                } catch (ytError) {
                    console.warn('Erro ao criar player do YouTube:', ytError);
                    this.state.youtubeAvailable = false;
                    this.setupAlternativePlayer();
                    resolve(); // Continua mesmo com erro
                }
            } catch (error) {
                console.warn('Erro geral ao configurar player:', error);
                this.state.youtubeAvailable = false;
                this.setupAlternativePlayer();
                resolve(); // Continua mesmo com erro
            }
        });
    }
    
    // Configura uma alternativa quando o YouTube não está disponível
    setupAlternativePlayer() {
        console.log('Configurando modo alternativo de player');
        
        // Oculta o container do YouTube e mostra uma mensagem alternativa
        if (this.elements.youtubePlayer) {
            this.elements.youtubePlayer.innerHTML = '<div class="p-4 bg-gray-100 rounded text-center">YouTube não disponível no momento</div>';
        }
        
        // Define métodos de fallback para as funções do player
        this.player = {
            playVideo: () => console.log('Play não disponível'),
            pauseVideo: () => console.log('Pause não disponível'),
            stopVideo: () => console.log('Stop não disponível'),
            getPlayerState: () => -1, // Estado indefinido
            getDuration: () => 0,
            getCurrentTime: () => 0,
            seekTo: () => console.log('Seek não disponível')
        };
    }

    // Configura os event listeners
    setupEventListeners() {
        // Play/Pause
        this.elements.playPauseBtn?.addEventListener('click', () => this.togglePlay());

        // Próxima/Anterior
        this.elements.prevBtn?.addEventListener('click', () => this.playPrevious());
        this.elements.nextBtn?.addEventListener('click', () => this.playNext());

        // Volume
        this.elements.volumeControl?.addEventListener('input', (e) => {
            this.setVolume(parseInt(e.target.value, 10));
        });

        // Progresso
        this.elements.progressContainer?.addEventListener('click', (e) => {
            const rect = e.currentTarget.getBoundingClientRect();
            const ratio = (e.clientX - rect.left) / rect.width;
            this.seekTo(ratio);
        });
    }

    // Handlers dos eventos do player
    handlePlayerReady(event) {
        console.log('Player pronto para uso');
        this.player = event.target;
        this.setVolume(this.state.volume);
        this.startProgressUpdate();
    }

    handlePlayerStateChange(event) {
        if (!event || !YT) return;

        switch (event.data) {
            case YT.PlayerState.ENDED:
                this.playNext();
                break;
            case YT.PlayerState.PLAYING:
                this.state.playing = true;
                this.updatePlayPauseButton();
                this.startProgressUpdate();
                break;
            case YT.PlayerState.PAUSED:
                this.state.playing = false;
                this.updatePlayPauseButton();
                this.stopProgressUpdate();
                break;
        }
    }

    handlePlayerError(event) {
        const errorMessages = {
            2: 'Parâmetro inválido',
            5: 'Erro de HTML5',
            100: 'Vídeo não encontrado',
            101: 'Reprodução não permitida',
            150: 'Reprodução não permitida'
        };

        const message = errorMessages[event.data] || 'Erro desconhecido';
        console.error('Erro do YouTube:', message);
        this.showError(`Não foi possível reproduzir esta faixa (${message})`);
        this.playNext();
    }

    // Métodos de controle do player
    async loadPlaylist(tracks) {
        if (!Array.isArray(tracks) || tracks.length === 0) {
            this.showError('Playlist inválida');
            return;
        }

        try {
            // Aguarda inicialização se necessário
            if (!this.state.initialized) {
                await this.init();
            }

            this.playlist = tracks;
            this.currentIndex = 0;
            await this.loadTrack(tracks[0]);
            this.showPlayer();
        } catch (error) {
            console.error('Erro ao carregar playlist:', error);
            this.showError('Erro ao carregar playlist');
        }
    }

    async loadTrack(track) {
        if (!track?.youtube_url) {
            throw new Error('Track inválida');
        }

        try {
            const videoId = this.extractVideoId(track.youtube_url);
            if (!videoId) throw new Error('ID do vídeo inválido');

            this.currentTrack = track;

            if (this.player?.loadVideoById) {
                this.player.loadVideoById(videoId);
                this.updatePlayerInfo();
            } else {
                throw new Error('Player não está pronto');
            }
        } catch (error) {
            console.error('Erro ao carregar track:', error);
            throw error;
        }
    }

    // Utilitários
    getPlayerElements() {
        return {
            container: document.getElementById('audio-player'),
            youtubePlayer: document.getElementById('youtube-player'),
            playPauseBtn: document.getElementById('play-pause-btn'),
            prevBtn: document.getElementById('prev-btn'),
            nextBtn: document.getElementById('next-btn'),
            volumeControl: document.getElementById('volume-control'),
            progressContainer: document.getElementById('progress-container'),
            progressBar: document.getElementById('progress-bar'),
            currentTime: document.getElementById('current-time'),
            duration: document.getElementById('duration'),
            title: document.getElementById('track-title'),
            artist: document.getElementById('track-artist'),
            cover: document.getElementById('album-cover'),
            playIcon: document.getElementById('play-icon'),
            pauseIcon: document.getElementById('pause-icon')
        };
    }

    extractVideoId(url) {
        try {
            const urlObj = new URL(url);
            const searchParams = new URLSearchParams(urlObj.search);

            if (urlObj.hostname.includes('youtu.be')) {
                return urlObj.pathname.slice(1);
            }

            return searchParams.get('v');
        } catch {
            // Tenta extrair o ID diretamente se a URL estiver malformada
            const match = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i);
            return match ? match[1] : null;
        }
    }

    showError(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-5 right-5 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }

    showPlayer() {
        this.elements.container?.classList.remove('hidden');
    }

    // Controles de reprodução
    togglePlay() {
        if (!this.player) return;

        if (this.state.playing) {
            this.player.pauseVideo();
        } else {
            this.player.playVideo();
        }
    }

    updatePlayPauseButton() {
        if (!this.elements.playIcon || !this.elements.pauseIcon) return;

        if (this.state.playing) {
            this.elements.playIcon.classList.add('hidden');
            this.elements.pauseIcon.classList.remove('hidden');
        } else {
            this.elements.playIcon.classList.remove('hidden');
            this.elements.pauseIcon.classList.add('hidden');
        }
    }

    playNext() {
        if (this.currentIndex < this.playlist.length - 1) {
            this.currentIndex++;
            this.loadTrack(this.playlist[this.currentIndex]);
        }
    }

    playPrevious() {
        if (this.currentIndex > 0) {
            this.currentIndex--;
            this.loadTrack(this.playlist[this.currentIndex]);
        }
    }

    setVolume(volume) {
        this.state.volume = volume;
        this.player?.setVolume(volume);

        if (this.elements.volumeControl) {
            this.elements.volumeControl.value = volume;
        }
    }

    seekTo(ratio) {
        if (!this.player) return;

        const duration = this.player.getDuration();
        const newTime = duration * ratio;
        this.player.seekTo(newTime, true);
    }

    startProgressUpdate() {
        this.stopProgressUpdate();
        this.progressInterval = setInterval(() => this.updateProgress(), 1000);
    }

    stopProgressUpdate() {
        if (this.progressInterval) {
            clearInterval(this.progressInterval);
            this.progressInterval = null;
        }
    }

    updateProgress() {
        if (!this.player || !this.elements.progressBar) return;

        try {
            const duration = this.player.getDuration() || 0;
            const current = this.player.getCurrentTime() || 0;
            const progress = (current / duration) * 100;

            this.elements.progressBar.style.width = `${progress}%`;

            if (this.elements.currentTime) {
                this.elements.currentTime.textContent = this.formatTime(current);
            }
            if (this.elements.duration) {
                this.elements.duration.textContent = this.formatTime(duration);
            }
        } catch (error) {
            console.error('Erro ao atualizar progresso:', error);
        }
    }

    formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);
        return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
    }

    updatePlayerInfo() {
        if (!this.currentTrack) return;

        try {
            // Atualiza título e artista
            if (this.elements.title) {
                this.elements.title.textContent = this.currentTrack.name || 'Desconhecido';
            }
            if (this.elements.artist) {
                this.elements.artist.textContent = this.currentTrack.artist || 'Artista Desconhecido';
            }

            // Atualiza imagem com verificação e fallback
            if (this.elements.cover) {
                const defaultCover = '/images/default-cover.jpg'; // Ajuste para seu caminho padrão

                // Função para verificar se a imagem existe
                const checkImage = (url) => {
                    return new Promise((resolve) => {
                        const img = new Image();
                        img.onload = () => resolve(true);
                        img.onerror = () => resolve(false);
                        img.src = url;
                    });
                };

                // Tenta carregar a imagem com fallback
                const loadCoverImage = async () => {
                    if (!this.currentTrack.cover_url) {
                        this.elements.cover.src = defaultCover;
                        return;
                    }

                    const imageExists = await checkImage(this.currentTrack.cover_url);
                    this.elements.cover.src = imageExists ? this.currentTrack.cover_url : defaultCover;
                };

                // Carrega a imagem apenas uma vez
                if (this.elements.cover.dataset.currentTrack !== this.currentTrack.id) {
                    loadCoverImage();
                    this.elements.cover.dataset.currentTrack = this.currentTrack.id;
                }
            }
        } catch (error) {
            console.error('Erro ao atualizar informações do player:', error);
        }
    }
}

// Inicialização única do player
if (!window.audioPlayer) {
    window.audioPlayer = new AudioPlayer();
}
