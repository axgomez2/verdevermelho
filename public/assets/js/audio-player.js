console.log("Audio player script loaded")

class AudioPlayer {
  constructor() {
    this.player = null
    this.currentTrack = null
    this.playlist = []
    this.currentIndex = 0
    this.isReady = false
    this.progressInterval = null
    this.onYouTubeIframeAPIReady = this.onYouTubeIframeAPIReady.bind(this)

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
      console.log("Waiting for YouTube API to be fully loaded...")
      setTimeout(() => this.initializeYouTubeAPI(), 100)
    }
  }

  onYouTubeIframeAPIReady() {
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

  onPlayerReady(event) {
    console.log("YouTube player is ready")
    this.isReady = true
    this.updateVolumeFromControl()
  }

  initializePlayerControls() {
    document.getElementById("play-pause-btn").addEventListener("click", () => this.togglePlay())
    document.getElementById("next-btn").addEventListener("click", () => this.playNext())
    document.getElementById("prev-btn").addEventListener("click", () => this.playPrevious())
    document.getElementById("volume-control").addEventListener("input", () => this.updateVolumeFromControl())

    this.initializeProgressBar()
    this.initializeVinylCards()
  }

  initializeProgressBar() {
    const progressContainer = document.getElementById("progress-container")
    const progressBar = document.getElementById("progress-bar")
    const progressHandle = document.getElementById("progress-handle")

    let isDragging = false

    const onMouseMove = (e) => {
      if (!isDragging) return
      const percentage = this.calculatePercentage(e.clientX, progressContainer)
      this.setProgress(percentage)
    }

    const onMouseUp = (e) => {
      if (isDragging) {
        isDragging = false
        const percentage = this.calculatePercentage(e.clientX, progressContainer)
        this.seekToPercentage(percentage)
      }
      document.removeEventListener("mousemove", onMouseMove)
      document.removeEventListener("mouseup", onMouseUp)
    }

    progressHandle.addEventListener("mousedown", (e) => {
      isDragging = true
      document.addEventListener("mousemove", onMouseMove)
      document.addEventListener("mouseup", onMouseUp)
    })

    progressContainer.addEventListener("click", (e) => {
      const percentage = this.calculatePercentage(e.clientX, progressContainer)
      this.seekToPercentage(percentage)
    })

    // Touch events
    const onTouchMove = (e) => {
      if (!isDragging) return
      const touch = e.touches[0]
      const percentage = this.calculatePercentage(touch.clientX, progressContainer)
      this.setProgress(percentage)
    }

    const onTouchEnd = (e) => {
      if (isDragging) {
        isDragging = false
        const touch = e.changedTouches[0]
        const percentage = this.calculatePercentage(touch.clientX, progressContainer)
        this.seekToPercentage(percentage)
      }
      document.removeEventListener("touchmove", onTouchMove)
      document.removeEventListener("touchend", onTouchEnd)
    }

    progressHandle.addEventListener("touchstart", (e) => {
      isDragging = true
      document.addEventListener("touchmove", onTouchMove)
      document.addEventListener("touchend", onTouchEnd)
    })

    progressContainer.addEventListener("touchstart", (e) => {
      const touch = e.touches[0]
      const percentage = this.calculatePercentage(touch.clientX, progressContainer)
      this.seekToPercentage(percentage)
    })

    progressContainer.addEventListener("mousemove", (e) => this.showProgressPreview(e))
    progressContainer.addEventListener("mouseleave", () => this.hideProgressPreview())
  }

  initializeVinylCards() {
    const vinylCards = document.querySelectorAll(".vinyl-card")
    vinylCards.forEach((card) => {
      const playButton = card.querySelector(".play-button")
      if (playButton) {
        playButton.addEventListener("click", () => {
          const tracks = JSON.parse(playButton.dataset.tracks || "[]")
          const artist = playButton.dataset.artist
          const coverUrl = playButton.dataset.coverUrl
          const vinylTitle = playButton.dataset.vinylTitle

          const validTracks = tracks
            .filter((track) => track.youtube_url)
            .map((track) => ({
              id: track.id,
              name: track.name,
              artist: artist,
              youtube_url: track.youtube_url,
              cover_url: coverUrl,
              vinyl_title: vinylTitle,
            }))

          if (validTracks.length > 0) {
            this.loadPlaylist(validTracks)
          } else {
            console.error("No playable tracks found for this vinyl")
          }
        })
      }
    })
  }

  calculatePercentage(clientX, element) {
    const rect = element.getBoundingClientRect()
    const x = clientX - rect.left
    return Math.max(0, Math.min(100, (x / element.offsetWidth) * 100))
  }

  loadPlaylist(tracks) {
    console.log("Loading playlist:", tracks)
    this.playlist = tracks
    this.currentIndex = 0
    if (this.playlist.length > 0) {
      this.loadTrack(this.playlist[0])
    } else {
      console.error("No playable tracks in the playlist")
    }
  }

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

  extractVideoId(url) {
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/
    const match = url.match(regExp)
    return match && match[2].length === 11 ? match[2] : null
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

  updatePlayerInfo() {
    console.log("Updating player info:", this.currentTrack)
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
      coverElement.onerror = function () {
        this.src = "/images/default-cover.jpg"
      }
    }

    console.log("Cover URL:", this.currentTrack.cover_url)
  }

  updatePlayPauseButton(isPlaying) {
    const playIcon = document.getElementById("play-icon")
    const pauseIcon = document.getElementById("pause-icon")
    if (isPlaying) {
      playIcon.classList.add("hidden")
      pauseIcon.classList.remove("hidden")
    } else {
      playIcon.classList.remove("hidden")
      pauseIcon.classList.add("hidden")
    }
  }

  showPlayer() {
    const playerElement = document.getElementById("audio-player")
    if (playerElement) {
      playerElement.classList.remove("hidden")
      console.log("Player shown")
    } else {
      console.error("Audio player element not found")
    }
  }

  updateProgressBar(isPlaying) {
    if (this.progressInterval) {
      clearInterval(this.progressInterval)
    }

    if (isPlaying) {
      this.progressInterval = setInterval(() => {
        if (this.player && this.player.getCurrentTime && this.player.getDuration) {
          const currentTime = this.player.getCurrentTime()
          const duration = this.player.getDuration()
          const progressPercentage = (currentTime / duration) * 100

          this.setProgress(progressPercentage)
          document.getElementById("current-time").textContent = this.formatTime(currentTime)
          document.getElementById("duration").textContent = this.formatTime(duration)
        }
      }, 1000)
    }
  }

  setProgress(percentage) {
    const progressBar = document.getElementById("progress-bar")
    const progressHandle = document.getElementById("progress-handle")
    progressBar.style.width = `${percentage}%`
    progressHandle.style.left = `${percentage}%`
  }

  seekToPercentage(percentage) {
    if (this.player && this.player.getDuration && this.player.seekTo) {
      const duration = this.player.getDuration()
      const seekTime = (percentage / 100) * duration
      this.player.seekTo(seekTime, true)
      this.setProgress(percentage)
    }
  }

  formatTime(time) {
    const minutes = Math.floor(time / 60)
    const seconds = Math.floor(time % 60)
    return `${minutes}:${seconds.toString().padStart(2, "0")}`
  }

  updateVolumeFromControl() {
    const volumeControl = document.getElementById("volume-control")
    if (this.player && this.player.setVolume) {
      this.player.setVolume(volumeControl.value)
    }
  }

  showProgressPreview(e) {
    const progressContainer = document.getElementById("progress-container")
    const percentage = this.calculatePercentage(e.clientX, progressContainer)
    this.setProgressPreview(percentage)
  }

  hideProgressPreview() {
    const progressPreview = document.getElementById("progress-preview")
    if (progressPreview) {
      progressPreview.style.display = "none"
    }
  }

  setProgressPreview(percentage) {
    const progressPreview = document.getElementById("progress-preview")
    if (progressPreview) {
      progressPreview.style.width = `${percentage}%`
      progressPreview.style.display = "block"
    }
  }
}

// Initialize AudioPlayer
const audioPlayer = new AudioPlayer()
window.audioPlayer = audioPlayer

console.log("AudioPlayer initialized:", window.audioPlayer)

