// Classe base do AudioPlayer com funcionalidades universais
class AudioPlayer {
    constructor() {
      this.player = null
      this.currentTrack = null
      this.playlist = []
      this.currentIndex = 0
      this.isReady = false
      this.progressInterval = null
      this.onYouTubeIframeAPIReady = this.onYouTubeIframeAPIReady.bind(this)

      this.initializationTimeout = setTimeout(() => {
        if (!this.isReady) {
          console.error("Player failed to initialize after 10 seconds")
          this.handleInitializationError()
        }
      }, 10000)

      this.initializeYouTubeAPI()
    }

    initializeYouTubeAPI() {
      if (!window.YT) {
        const tag = document.createElement("script")
        tag.src = "https://www.youtube.com/iframe_api"
        const firstScriptTag = document.getElementsByTagName("script")[0]
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag)

        window.onYouTubeIframeAPIReady = () => {
          console.log("YouTube API is ready")
          this.onYouTubeIframeAPIReady()
        }
      } else if (window.YT && window.YT.Player) {
        this.onYouTubeIframeAPIReady()
      } else {
        console.log("Waiting for YouTube API...")
        setTimeout(() => this.initializeYouTubeAPI(), 100)
      }
    }

    onYouTubeIframeAPIReady() {
      if (typeof YT == "undefined" || typeof YT.Player == "undefined") {
        window.setTimeout(this.onYouTubeIframeAPIReady, 100)
      } else {
        this.player = new YT.Player("youtube-player", {
          height: "0",
          width: "0",
          events: {
            onReady: this.onPlayerReady.bind(this),
            onStateChange: this.onPlayerStateChange.bind(this),
            onError: this.onPlayerError.bind(this),
          },
        })
        this.initializePlayerControls()
      }
    }

    // Métodos básicos de controle
    loadTrack(track) {
      console.log("Loading track:", track)
      this.currentTrack = track
      const videoId = this.extractVideoId(track.youtube_url)
      if (videoId && this.player && this.player.loadVideoById) {
        this.player.loadVideoById(videoId)
        this.updatePlayerInfo()
        this.showPlayer()
      } else {
        console.error("Invalid YouTube URL or player not ready")
      }
    }

    loadPlaylist(tracks) {
      if (!Array.isArray(tracks) || tracks.length === 0) {
        console.error("Invalid tracks array")
        return
      }

      this.playlist = tracks
      this.currentIndex = 0
      this.loadTrack(this.playlist[0])
    }

    togglePlay() {
      if (this.player && this.isReady) {
        if (this.player.getPlayerState() === YT.PlayerState.PLAYING) {
          this.player.pauseVideo()
        } else {
          this.player.playVideo()
        }
      }
    }

    playNext() {
      if (this.currentIndex < this.playlist.length - 1) {
        this.currentIndex++
        this.loadTrack(this.playlist[this.currentIndex])
      } else if (this.playlist.length > 0) {
        this.currentIndex = 0
        this.loadTrack(this.playlist[this.currentIndex])
      }
    }

    playPrevious() {
      if (this.currentIndex > 0) {
        this.currentIndex--
        this.loadTrack(this.playlist[this.currentIndex])
      } else if (this.playlist.length > 0) {
        this.currentIndex = this.playlist.length - 1
        this.loadTrack(this.playlist[this.currentIndex])
      }
    }

    // Métodos utilitários
    extractVideoId(url) {
      const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/
      const match = url.match(regExp)
      return match && match[2].length === 11 ? match[2] : null
    }

    formatTime(time) {
      const minutes = Math.floor(time / 60)
      const seconds = Math.floor(time % 60)
      return `${minutes}:${seconds.toString().padStart(2, "0")}`
    }

    // Métodos de UI básicos
    showPlayer() {
      const playerElement = document.getElementById("audio-player")
      if (playerElement) {
        playerElement.classList.remove("hidden")
      }
    }

    updatePlayerInfo() {
      const titleElement = document.getElementById("track-title")
      const artistElement = document.getElementById("track-artist")
      const coverElement = document.getElementById("album-cover")

      if (titleElement) titleElement.textContent = this.currentTrack.name || "Unknown Track"
      if (artistElement) {
        artistElement.textContent = `${this.currentTrack.artist || "Unknown Artist"} - ${this.currentTrack.vinyl_title || "Unknown Album"}`
      }
      if (coverElement) {
        coverElement.src = this.currentTrack.cover_url || "/images/default-cover.jpg"
        coverElement.alt = `${this.currentTrack.vinyl_title || "Album"} cover`
      }
    }

    // Event handlers básicos
    onPlayerReady(event) {
      console.log("YouTube player is ready")
      this.isReady = true
      clearTimeout(this.initializationTimeout)
      this.updateVolumeFromControl()
    }

    onPlayerStateChange(event) {
      if (event.data === YT.PlayerState.ENDED) {
        this.playNext()
      }
      this.updatePlayPauseButton(event.data === YT.PlayerState.PLAYING)
      this.updateProgressBar(event.data === YT.PlayerState.PLAYING)
    }

    onPlayerError(event) {
      console.error("YouTube Player Error:", event.data)
    }

    handleInitializationError() {
      alert("Erro ao inicializar o player. Por favor, recarregue a página.")
    }
  }

  // Exporta a classe para uso global
  window.AudioPlayer = AudioPlayer

