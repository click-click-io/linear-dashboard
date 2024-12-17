<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetricsController extends Controller
{
    public function getMetrics(Request $request)
    {
        // Get token from URL parameter
        $token = $request->query('linear-token');
        
        if (!$token) {
            return response()->json(['error' => 'No token provided'], 400);
        }

        Log::debug('Received request for metrics with token: ' . $token);

        // Define your GraphQL queries here
        $query = '
            query {
                issues(filter: {
                    state: { type: { neq: "backlog" } }
                }) {
                    nodes {
                        id
                        identifier
                        title
                        state {
                            name
                            type
                        }
                        assignee {
                            name
                            avatarUrl
                        }
                        team {
                            name
                        }
                        project {
                            name
                        }
                        dueDate
                    }
                }
            }
        ';

        // Log the query for debugging
        Log::debug('Linear API query: ' . $query);
        Log::debug('Executing GraphQL query: ' . $query);

        // Make the request to the Linear API
        $response = Http::withHeaders([
            'Authorization' => $token,
            'Content-Type' => 'application/json',
        ])->post('https://api.linear.app/graphql', [
            'query' => $query,
        ]);

        // Log the full response for debugging
        Log::debug('Linear API response: ' . $response->body());

        // Handle the response and return the metrics
        if ($response->failed()) {
            Log::debug('Linear API error response: ' . $response->body()); // Log the error response
            return response()->json(['error' => $response->json()], $response->status());
        }

        $data = $response->json();
        Log::debug('API Response: ' . json_encode($data));
        // Return the relevant data to the frontend
        return response()->json([
            'totalOpen' => count($data['data']['issues']['nodes']),
            'inProgress' => count(array_filter($data['data']['issues']['nodes'], fn($issue) => strtolower($issue['state']['name']) === 'in progress')),
            'inReview' => count(array_filter($data['data']['issues']['nodes'], fn($issue) => strtolower($issue['state']['name']) === 'in review')),
            'dueTickets' => count(array_filter($data['data']['issues']['nodes'], fn($issue) => isset($issue['dueDate']) && $issue['dueDate'] !== null && new \DateTime($issue['dueDate']) < new \DateTime())),
            'issues' => $data['data']['issues']['nodes'],
        ]);
    }

    public function showIssues(Request $request)
    {
        // Retrieve the Linear API token from the request
        $token = $request->query('linear-token');

        if (!$token) {
            return response()->json(['error' => 'No token provided'], 400);
        }

        // Fetch issues from the Linear API
        $issues = $this->fetchIssues($token);

        // Pass the issues to the view
        return view('issues', ['issues' => $issues]);
    }

    private function fetchIssues($token)
    {
        // Implement the logic to fetch issues from the Linear API
        $query = '
        query {
            issues(filter: {
                state: { type: { neq: "backlog" } }
            }) {
                nodes {
                    id
                    identifier
                    title
                    state {
                        name
                        type
                    }
                    assignee {
                        name
                        avatarUrl
                    }
                    team {
                        name
                    }
                    project {
                        name
                    }
                    dueDate
                }
            }
        }
        ';

        $response = Http::withHeaders([
            'Authorization' => $token,
            'Content-Type' => 'application/json',
        ])->post('https://api.linear.app/graphql', [
            'query' => $query,
        ]);

        return $response->json()['data']['issues']['nodes'] ?? [];
    }
}
