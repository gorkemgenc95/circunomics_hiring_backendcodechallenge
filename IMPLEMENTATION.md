# Implementation and Conceptual Questions

## 1. How were you debugging this mini-project? Which tools?

### Debugging Approach and Tools Used

**Debugging Tools:**
- **PHPUnit**: Writing tests helped identify issues early and validate behavior.
- **Docker Logs**: Used `make docker-logs` to monitor application and database container logs for runtime errors.
- **Error Logs**: PHP error logging to track exceptions and runtime issues.
- **Browser Developer Tools**: For debugging the web interface, inspecting network requests, and CSS issues.

**Debugging Workflow:**
1. **Test-First Approach**: Wrote unit tests to isolate and verify component behavior
2. **Integration Testing**: Used integration tests to verify API connections and database interactions
3. **Docker Environment**: Used containerized environment for consistent debugging across systems

**Specific Debugging Scenarios:**
- **API Connection Issues**: Used GitHub API documentation while building the service
- **Database Problems**: Monitored MySQL logs through Docker
- **Service Layer Issues**: Implemented logging in service classes

**Code Quality Tools:**
- **PHP CS Fixer**: For consistent code style (`make cs-fix`)
- **Static Analysis**: Manual code review
- **Test Scripts**: Manually written scripts to check API and SQL connections

## 2. Please give a detailed answer on your approach to test this mini-project.

### Testing Strategy

**Multi-Layer Testing Approach:**

#### Unit Tests
```
tests/Unit/
├── Api/                 # API client testing
├── Factories/           # Model factory testing  
├── Repositories/        # Data access layer testing
└── Services/            # Business logic testing
```

**Key Testing Principles:**
1. **Isolation**: Each unit test focuses on a single class/method
2. **Mocking**: External dependencies (API, database) are mocked for reliable tests
3. **Edge Cases**: Testing both happy case scenarios and error conditions
4. **Data Validation**: Ensuring proper data transformation and validation

**Unit Test Examples:**
- **API Client Tests**: Mock HTTP responses to test GitHub API integration
- **Repository Tests**: Mock database connections to test data access patterns
- **Service Tests**: Mock dependencies to test business logic in isolation
- **Factory Tests**: Validate model creation and data transformation

#### Integration Tests
```
tests/Integration/
├── Api/                 # Real API integration testing
└── Services/            # End-to-end service testing
```

**Integration Test Focus:**
- **Real API Calls**: Testing actual GitHub API connectivity

**Testing Patterns Used:**
1. Dependency Injection Testing
2. Mock Verification
3. State Testing
4. Behavior Testing

## 3. Imagine this mini-project needs microservices with one single database; how would you draft an architecture?

### Microservices Architecture Design

#### High-Level Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│  Commit Sync    │    │  Commit Query   │    │     Web UI      │
│    Service      │    │    Service      │    │    Service      │
│  (Port 8001)    │    │  (Port 8002)    │    │   (Port 8003)   │
└─────────┬───────┘    └─────────┬───────┘    └─────────┬───────┘
          │                      │                      │
          └──────────────────────┼──────────────────────┘
                                 │
                                 ▼
                        ┌─────────────────┐
                        │   Shared MySQL  │
                        │   Database      │
                        │   (Port 3306)   │
                        └─────────────────┘
```

**1. Commit Sync Service** (Port 8001)
- **Responsibility**: GitHub API integration, commit fetching, data synchronization
- **API Endpoints**:
  ```
  POST /api/sync/{owner}/{repo}     # Trigger sync for specific repo
  GET  /api/sync/status/{jobId}     # Check sync status
  POST /api/sync/batch              # Batch sync multiple repos
  ```
- **Features**:
  - Rate limiting
  - Retry mechanisms
  - Background job processing
  - Duplicate detection

**2. Commit Query Service** (Port 8002)
- **Responsibility**: Data retrieval, filtering, pagination
- **API Endpoints**:
  ```
  GET  /api/commits                 # Paginated commit list
  GET  /api/commits/{hash}          # Single commit details
  GET  /api/commits/author/{name}   # Commits by author
  ```
- **Features**:
  - Caching layer
  - Advanced filtering

**3. Web UI Service** (Port 8003)
- **Responsibility**: User interface, frontend rendering
- **Features**:
  - API orchestration
  - User session management

## 4. How would your solution differ if when all of a sudden, instead of saving to a Database, you had to call another external API to store and receive the commits?

### Adaptation to External API Storage

#### Current vs. External API Architecture

Since we have implemented a flexible code, we can simply create a new commit repository extends CommitRepositoryInterface.
We may need to adjust our data format, handle rate limits and possible timeouts.

**Current Architecture:**
```
GitHub API --> Commit Sync Service --> MySQL Database
```

**New Architecture:**
```
GitHub API --> Commit Sync Service --> External Storage API
```
