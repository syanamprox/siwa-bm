@extends('layouts.app')

@section('title', 'Changelog - SIWA')

@section('content')
<!-- Changelog Page -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-code-branch mr-2"></i>Changelog
        </h6>
        <div>
            <button class="btn btn-info btn-sm" onclick="loadSystemInfo()">
                <i class="fas fa-info-circle fa-sm"></i> System Info
            </button>
            <button class="btn btn-success btn-sm" onclick="refreshData()">
                <i class="fas fa-sync-alt fa-sm"></i> Refresh
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- System Info Card (Hidden by default) -->
        <div id="systemInfoCard" class="alert alert-info" style="display: none;">
            <h6><i class="fas fa-server mr-2"></i>System Information</h6>
            <div id="systemInfoContent">
                Loading system information...
            </div>
        </div>

        <!-- Repository Info -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Current Branch</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="currentBranch">-</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Latest Tag</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="latestTag">-</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Commits</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalCommits">-</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Showing</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Last 50</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commit History Table -->
        <div class="table-responsive">
            <table class="table table-bordered" id="changelogTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Commit Hash</th>
                        <th>Date</th>
                        <th>Author</th>
                        <th>Message</th>
                        <th width="100">Actions</th>
                    </tr>
                </thead>
                <tbody id="changelogTableBody">
                    <tr>
                        <td colspan="5" class="text-center">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            Loading changelog data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Commit Detail Modal -->
<div class="modal fade" id="commitModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-code-branch mr-2"></i>Commit Detail
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="commitDetailContent">
                    Loading commit details...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.commit-detail-section {
    margin-bottom: 1.5rem;
}

.file-category {
    margin-bottom: 1rem;
}

.file-category ul {
    margin: 0.5rem 0;
    padding-left: 1.5rem;
}

.file-category li {
    margin: 0.25rem 0;
    font-size: 0.9rem;
}

.border-left-primary {
    border-left: 4px solid #4e73df !important;
}

.border-left-success {
    border-left: 4px solid #1cc88a !important;
}

.bg-success-light {
    background-color: #f8f9fc !important;
    border: 1px solid #e3e6f0;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    loadData();
});

function loadData() {
    $.ajax({
        url: '/admin/api/changelog',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                displaySystemInfo(response.data);
                renderTable(response.data.commits);
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function(xhr) {
            var message = xhr.responseJSON?.message || 'Failed to load changelog data';
            showToast(message, 'error');
        }
    });
}

function displaySystemInfo(data) {
    $('#currentBranch').text(data.current_branch || 'Unknown');
    $('#latestTag').text(data.latest_tag || 'No tags');
    $('#totalCommits').text(data.total_commits || 0);
}

function renderTable(commits) {
    var html = '';

    if (commits.length === 0) {
        html = '<tr><td colspan="5" class="text-center">No commits found</td></tr>';
    } else {
        commits.forEach(function(commit) {
            var commitHash = '<code class="text-primary">' + commit.short_hash + '</code>';
            var date = new Date(commit.date).toLocaleString('id-ID');
            var message = commit.message.length > 80 ?
                commit.message.substring(0, 80) + '...' :
                commit.message;

            html += `
                <tr>
                    <td>${commitHash}</td>
                    <td><small>${date}</small></td>
                    <td>${commit.author}</td>
                    <td>
                        <div>${message}</div>
                        ${commit.message.length > 80 ? '<small class="text-muted">Click "View" to see full message</small>' : ''}
                    </td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="viewCommitDetail('${commit.hash}')">
                            <i class="fas fa-eye fa-sm"></i> View
                        </button>
                    </td>
                </tr>
            `;
        });
    }

    $('#changelogTableBody').html(html);
}

function viewCommitDetail(hash) {
    $('#commitModal').modal('show');
    $('#commitDetailContent').html(`
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Loading commit details...</p>
        </div>
    `);

    $.ajax({
        url: '/admin/api/changelog/' + hash,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                displayCommitDetail(response.data);
            } else {
                $('#commitDetailContent').html('<div class="alert alert-danger">Failed to load commit details: ' + response.message + '</div>');
            }
        },
        error: function(xhr) {
            var message = xhr.responseJSON?.message || 'Failed to load commit details';
            $('#commitDetailContent').html('<div class="alert alert-danger">Error: ' + message + '</div>');
        }
    });
}

function displayCommitDetail(commit) {
    var date = new Date(commit.date).toLocaleString('id-ID');
    var filesHtml = '';
    var descriptionHtml = '';

    // Process commit body for better display
    if (commit.body && commit.body.trim()) {
        var bodyLines = commit.body.split('\n');
        var descriptionHtml = '';
        var formattedLines = [];

        bodyLines.forEach(function(line) {
            var trimmedLine = line.trim();

            // Skip Co-Authored-By lines and generated markers
            if (trimmedLine.startsWith('Co-Authored-By:') ||
                trimmedLine.startsWith('🤖 Generated with') ||
                trimmedLine.startsWith('Co-Authored-By:')) {
                return;
            }

            // Skip empty lines
            if (trimmedLine === '') {
                return;
            }

            // Process the line for display
            if (trimmedLine.startsWith('-') || trimmedLine.startsWith('*')) {
                var content = trimmedLine.substring(1).trim();
                if (content) {
                    // Check if it has sub-bullets
                    if (content.includes('*') || content.includes('–')) {
                        // Handle nested bullets
                        var subParts = content.split(/(?:\*|–)/);
                        if (subParts.length > 1) {
                            var mainPoint = subParts[0].trim();
                            var subPoints = subParts.slice(1).filter(p => p.trim()).map(p => p.trim());

                            formattedLines.push('<i class="fas fa-chevron-right text-primary mr-2"></i><strong>' + mainPoint + '</strong>');
                            subPoints.forEach(function(subPoint) {
                                formattedLines.push('<i class="fas fa-angle-right text-info mr-3 ml-2"></i>' + subPoint);
                            });
                        } else {
                            formattedLines.push('<i class="fas fa-chevron-right text-primary mr-2"></i>' + content);
                        }
                    } else {
                        formattedLines.push('<i class="fas fa-chevron-right text-primary mr-2"></i>' + content);
                    }
                }
            } else {
                // Regular line (description or header)
                if (trimmedLine.toLowerCase().includes('features:') ||
                    trimmedLine.toLowerCase().includes('technical improvements:') ||
                    trimmedLine.toLowerCase().includes('database updates:') ||
                    trimmedLine.toLowerCase().includes('ui/ux enhancements:') ||
                    trimmedLine.toLowerCase().includes('bug fixes:') ||
                    trimmedLine.toLowerCase().includes('fixes:')) {
                    formattedLines.push('<h6 class="text-primary font-weight-bold mt-3 mb-2">' + trimmedLine + '</h6>');
                } else {
                    formattedLines.push('<div class="mb-2">' + trimmedLine + '</div>');
                }
            }
        });

        if (formattedLines.length > 0) {
            descriptionHtml = `
                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong><i class="fas fa-file-alt mr-2"></i>Detail Perubahan:</strong><br>
                        <div class="bg-light p-3 rounded border-left-primary" style="border-left: 4px solid #4e73df; font-size: 14px; line-height: 1.6;">
                            ${formattedLines.join('')}
                        </div>
                    </div>
                </div>
            `;
        }
    }

  
    var html = `
        <div class="row mb-3">
            <div class="col-md-6">
                <strong><i class="fas fa-hashtag mr-2"></i>Commit Hash:</strong><br>
                <code class="bg-primary text-white px-2 py-1 rounded">${commit.hash}</code>
                <button class="btn btn-sm btn-outline-secondary ml-2" onclick="copyToClipboard('${commit.hash}')" title="Copy hash">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
            <div class="col-md-6">
                <strong><i class="fas fa-calendar-alt mr-2"></i>Date:</strong><br>
                <span class="badge badge-info">${date}</span>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <strong><i class="fas fa-user mr-2"></i>Author:</strong><br>
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-circle text-primary mr-2" style="font-size: 1.5em;"></i>
                    <span class="font-weight-bold">${commit.author}</span>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <strong><i class="fas fa-heading mr-2"></i>Judul Perubahan:</strong><br>
                <div class="alert alert-primary mb-0">
                    <h6 class="mb-0 font-weight-bold">${commit.subject}</h6>
                </div>
            </div>
        </div>
        ${descriptionHtml}
    `;

    $('#commitDetailContent').html(html);
}

// Helper function to copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showToast('Hash copied to clipboard!', 'success', 1500);
    }).catch(function() {
        // Fallback for older browsers
        var textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showToast('Hash copied to clipboard!', 'success', 1500);
    });
}

function loadSystemInfo() {
    var card = $('#systemInfoCard');

    if (card.is(':visible')) {
        card.slideUp();
        return;
    }

    $.ajax({
        url: '/admin/api/changelog/system-info',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                displaySystemInfoDetail(response.data);
                card.slideDown();
            } else {
                showToast('Failed to load system info', 'error');
            }
        },
        error: function(xhr) {
            showToast('Error loading system info', 'error');
        }
    });
}

function displaySystemInfoDetail(data) {
    var gitInfo = data.git_info || {};
    var html = `
        <div class="row">
            <div class="col-md-6">
                <strong>Laravel Version:</strong> ${data.laravel_version}<br>
                <strong>PHP Version:</strong> ${data.php_version}<br>
                <strong>Environment:</strong> <span class="badge badge-${data.environment === 'production' ? 'danger' : 'success'}">${data.environment}</span><br>
                <strong>App URL:</strong> <a href="${data.app_url}" target="_blank">${data.app_url}</a>
            </div>
            <div class="col-md-6">
                ${gitInfo.current_branch ? `<strong>Current Branch:</strong> ${gitInfo.current_branch}<br>` : ''}
                ${gitInfo.latest_commit ? `<strong>Latest Commit:</strong> <code>${gitInfo.latest_commit}</code><br>` : ''}
                ${gitInfo.total_commits ? `<strong>Total Commits:</strong> ${gitInfo.total_commits}<br>` : ''}
                ${gitInfo.last_update ? `<strong>Last Update:</strong> ${new Date(gitInfo.last_update).toLocaleString('id-ID')}<br>` : ''}
            </div>
        </div>
    `;

    $('#systemInfoContent').html(html);
}

function refreshData() {
    showToast('Refreshing changelog data...', 'info', 1000);
    loadData();
}
</script>
@endpush