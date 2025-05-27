# Setup Instructions

This project is a PHP web application that connects to the GitHub API to fetch and store commit data from repositories.

## Prerequisites

- Docker and Docker Compose
- Make (for using the provided Makefile commands)

## Quick Setup

The project includes a comprehensive Makefile with setup commands. To get started quickly:

```bash
# Clone the repository (if not already done)
git clone <repository-url>
cd circunomics_hiring_backendcodechallenge

# Complete setup (creates .env, starts Docker, runs migrations)
make setup
```

This single command will:
1. Create the `.env` file with default database configuration
2. Start the Docker containers (PHP app + MySQL database)
3. Wait for the database to be ready
4. Run database migrations

## Available Commands

The Makefile provides several useful commands:

### Development
- `make setup` - Complete setup (environment + Docker + migrations)
- `make install` - Install PHP dependencies via Composer
- `make update` - Update PHP dependencies

### Docker Management
- `make docker-up` - Start Docker containers
- `make docker-down` - Stop Docker containers
- `make docker-logs` - View Docker logs

### Database
- `make migrate` - Run database migrations

### Testing
- `make test` - Run all tests (unit + integration)
- `make test-unit` - Run unit tests only
- `make test-integration` - Run integration tests only

### Code Quality
- `make cs-fix` - Fix code style issues
- `make cs-check` - Check code style without fixing

### GitHub Integration
- `make sync-gh-commits` - Sync commits from GitHub (requires Docker to be running)

## Accessing the Application

After setup is complete:

1. **Web Interface**: Visit `http://localhost:8000` to view the main menu
2. **Commits Page**: Visit `http://localhost:8000/commits` to see paginated commits by author
3. **API**: The application exposes endpoints for commit data

## Project Structure

```
source/
├── app/
│   ├── Api/            # GitHub API client implementation
│   ├── Config/         # Database configuration
│   ├── Factories/      # Model factories
│   ├── Models/         # Eloquent models
│   ├── Repositories/   # Data access layer
│   └── Services/       # Business logic services
├── database/
│   └── migrations/     # Database migration files
├── public/             # Web-accessible files
├── scripts/            # Command-line scripts
└── tests/              # Unit and integration tests
```

## Syncing Commits

To fetch commits from GitHub repositories:

```bash
# Sync commits from nodejs/node repository (default)
make sync-gh-commits
```

## Database Schema

The application uses a `commits` table with the following structure:
- `id` - Primary key
- `hash` - Commit SHA hash (unique)
- `author` - Commit author name
- `date` - Commit date
- `repository_owner` - Repository owner (e.g., 'nodejs')
- `repository_name` - Repository name (e.g., 'node')
- `platform` - Git platform (e.g., 'github')
- `message` - Commit message
- `created_at`, `updated_at` - Timestamps

## Troubleshooting

### Database Connection Issues
If you see database connection errors:
1. Ensure Docker containers are running: `make docker-up`
2. Wait for the database to fully initialize (may take 30-60 seconds on first run)
3. Check Docker logs: `make docker-logs`

### Port Conflicts
If port 8000 is already in use:
1. Stop the conflicting service
2. Or modify the port in `docker-compose.yml`

### Permission Issues
If you encounter permission issues:
```bash
# Fix ownership of vendor directory
sudo chown -R $USER:$USER source/vendor
```

## Testing

```bash
# Run all tests
make test

# Run specific test types
make test-unit        # Unit tests only
make test-integration # Integration tests (requires database)
```

## Architecture

The application follows a clean architecture pattern:
- **Controllers** handle HTTP requests
- **Services** contain business logic
- **Repositories** handle data access
- **Models** represent data entities
- **API Clients** handle external API communication

The design supports multiple Git platforms (GitHub, GitLab, Bitbucket) through a common interface pattern. 